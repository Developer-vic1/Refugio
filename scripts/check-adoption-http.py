"""Verifica el flujo público con datos sintéticos y sin enviar mensajes externos."""
import http.cookiejar
import json
import re
import urllib.error
import urllib.parse
import urllib.request
from pathlib import Path

root = Path(__file__).resolve().parent.parent
fixture = json.loads((root / "output/pdf/test-input.json").read_text(encoding="utf-8"))
base = "http://127.0.0.1:8000"
path = "/adopciones/solicitar/" + str(fixture["animal_id"])
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
with opener.open(base + path, timeout=20) as response:
    html = response.read().decode("utf-8")
token = re.search(r'name="_csrf" value="([a-f0-9]+)"', html).group(1)
data = fixture["input"] | {"_csrf": token}
request = urllib.request.Request(base + path + "/pdf", data=urllib.parse.urlencode(data).encode(), headers={"Accept": "application/pdf, application/json"})
with opener.open(request, timeout=30) as response:
    content = response.read()
    assert response.headers.get_content_type() == "application/pdf"
    assert "attachment" in response.headers["Content-Disposition"]
    assert "no-store" in response.headers["Cache-Control"]
    assert content.startswith(b"%PDF-")
    (root / "output/pdf/adopcion-http.pdf").write_bytes(content)
for changed, expected in [({"edad": "17"}, 422), ({"_csrf": "invalido"}, 419)]:
    request = urllib.request.Request(base + path + "/pdf", data=urllib.parse.urlencode(data | changed).encode(), headers={"Accept": "application/json"})
    try:
        opener.open(request, timeout=20)
        raise AssertionError("Se aceptó una entrada inválida")
    except urllib.error.HTTPError as error:
        assert error.code == expected
        if expected == 422:
            assert "edad" in json.loads(error.read())["errors"]
print("HTTP: PDF descargable, caché privada, validación por campo y CSRF correctos.")
