<div align="center">

<a href="docs/RUTAS_Y_MODULOS.md">
  <img src="docs/assets/huella-serena-readme.svg" alt="Huella Serena: un lugar seguro, un nuevo comienzo. Identidad visual con huella en movimiento suave." width="100%">
</a>

<br/>

<strong>Una plataforma para cuidar, conectar y abrir nuevas oportunidades. 🐾</strong>

<p>Adopciones responsables · Historias · Comunidad · Transparencia</p>

<p>
  <a href="#descubre">Descubre</a> &nbsp;·&nbsp;
  <a href="#experiencia">Experiencia</a> &nbsp;·&nbsp;
  <a href="#tecnologias">Tecnologías</a> &nbsp;·&nbsp;
  <a href="#comenzar">Comenzar</a> &nbsp;·&nbsp;
  <a href="#participa">Participa</a>
</p>

<p>
  <a href="https://github.com/Developer-vic1/Refugio/commits/main"><img alt="Último commit de main" src="https://img.shields.io/github/last-commit/Developer-vic1/Refugio/main?style=for-the-badge&label=Actualizado&labelColor=17463D&color=89A88F"></a>
  <a href="https://github.com/Developer-vic1/Refugio/pulls"><img alt="Pull requests abiertos" src="https://img.shields.io/github/issues-pr/Developer-vic1/Refugio?style=for-the-badge&label=Pull%20requests&labelColor=17463D&color=D39A55"></a>
  <a href="https://github.com/Developer-vic1/Refugio/stargazers"><img alt="Estrellas de GitHub" src="https://img.shields.io/github/stars/Developer-vic1/Refugio?style=for-the-badge&label=Estrellas&labelColor=17463D&color=89A88F"></a>
</p>

</div>

---

<a id="descubre"></a>
## 🌿 Descubre

**Huella Serena** es una iniciativa de bienestar animal en formación, acompañada de una plataforma web para compartir historias, encontrar posibles hogares y organizar la colaboración de la comunidad.

| 🐾 Explorar | 🤝 Colaborar | 🛡️ Administrar |
| :---: | :---: | :---: |
| Animales, fichas e historias | Campañas, donaciones y voluntariado | Solicitudes, contenido y operaciones |
| [Ver rutas públicas](docs/RUTAS_Y_MODULOS.md) | [Conocer los módulos](docs/RUTAS_Y_MODULOS.md) | [Explorar MVC](docs/ARQUITECTURA.md) |

<a id="experiencia"></a>
## ✨ Una experiencia con personalidad

<table>
<tr>
<td width="50%" align="center">
  <img src="public/assets/img/inicio-paseo-v2.webp" width="100%" alt="Arte editorial: un perro caminando entre plantas bajo la luz de la mañana.">
  <br><strong>Una bienvenida que conecta</strong>
  <br><sub>Diseño editorial, fotografía protagonista y navegación accesible.</sub>
</td>
<td width="50%" align="center">
  <img src="public/assets/img/historias-ventana-v2.webp" width="100%" alt="Arte editorial: un gato junto a una ventana con luz cálida.">
  <br><strong>Cada historia importa</strong>
  <br><sub>Galerías, relatos, interacciones y movimiento consciente.</sub>
</td>
</tr>
</table>

<div align="center">
  <sub>Imágenes conceptuales creadas para la interfaz; no representan casos reales de rescate.</sub>
</div>

<details>
<summary><strong>🎨 Explorar el diseño y las animaciones</strong></summary>

La identidad visual combina **verde bosque, salvia, marfil y acentos miel**. Incluye un sistema de temas, tarjetas interactivas, transiciones y un motor de movimiento progresivo compatible con <code>prefers-reduced-motion</code>.

[Ver Hyperframes y movimiento](docs/modules/MOTION_ENGINE.md) · [Ver sistema UI](docs/modules/UI_FOUNDATION.md)

</details>

<a id="tecnologias"></a>
## 🧩 Tecnología que da vida al proyecto

<div align="center">

<img alt="PHP 8.2+" src="https://img.shields.io/badge/PHP-8.2%2B-17463D?style=flat-square&logo=php&logoColor=white">
<img alt="MySQL / MariaDB" src="https://img.shields.io/badge/MySQL%20%2F%20MariaDB-17463D?style=flat-square&logo=mysql&logoColor=white">
<img alt="Tailwind CSS 4" src="https://img.shields.io/badge/Tailwind%20CSS-v4-17463D?style=flat-square&logo=tailwindcss&logoColor=white">
<img alt="JavaScript" src="https://img.shields.io/badge/JavaScript-17463D?style=flat-square&logo=javascript&logoColor=white">
<img alt="MVC modular" src="https://img.shields.io/badge/MVC-Modular-D39A55?style=flat-square">
<img alt="Laragon" src="https://img.shields.io/badge/Laragon-Windows-D39A55?style=flat-square">

</div>

<details>
<summary><strong>🧭 Ver la arquitectura en 10 segundos</strong></summary>

```text
Navegador → public/index.php → Router → Controller
                                      ↓
                            Service / Repository
                                      ↓
                                  MySQL
                                      ↓
                          Vista PHP + Tailwind + JS
```

La carpeta <code>public/</code> es la única raíz web. <code>storage/</code> protege imágenes y documentos; <code>resources/</code> concentra los estilos y animaciones.

[Documentación de arquitectura](docs/ARQUITECTURA.md) · [Seguridad](docs/SEGURIDAD_Y_PUBLICACION.md)

</details>

<a id="comenzar"></a>
## 🚀 Comenzar

<details open>
<summary><strong>Instalación local · Laragon / Windows</strong></summary>

Necesitas **PHP 8.2+**, Composer, Node.js, MySQL y la base <code>refugio</code> ya creada (22 tablas y 3 vistas). Copia <code>.env.example</code> a <code>.env</code> y configura tu conexión.

```powershell
git clone https://github.com/Developer-vic1/Refugio.git
cd Refugio
Copy-Item .env.example .env
composer install
npm install
npm run build
php start
```

También puedes utilizar <code>Iniciar Refugio.cmd</code> en Windows. Para abrirlo con Apache de Laragon, configura el **DocumentRoot hacia <code>public/</code>**.

</details>

<a id="participa"></a>
## 💚 Construyamos mejores comienzos

**Estado:** V2 en consolidación. El sitio público y varios módulos administrativos están desarrollados; la formalización completa de adopciones, inventario, finanzas, permisos y validación integral siguen pendientes. **Todavía no es una plataforma lista para producción.**

<div align="center">

<a href="docs/progress/EJECUCION_V2.md"><strong>📍 Ver progreso</strong></a>
&nbsp;&nbsp;·&nbsp;&nbsp;
<a href="https://github.com/Developer-vic1/Refugio/issues/new"><strong>💡 Proponer mejora</strong></a>
&nbsp;&nbsp;·&nbsp;&nbsp;
<a href="https://github.com/Developer-vic1/Refugio/pulls"><strong>🔀 Pull requests</strong></a>

<p><sub>Ramas <code>feature/*</code> → <code>develop</code> → <code>main</code> · Sin datos personales, credenciales ni expedientes privados en Git.</sub></p>

<strong>Hecho con propósito por <a href="https://github.com/Developer-vic1">@Developer-vic1</a> 🌱</strong>

</div>
