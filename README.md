<div align="center">

<img src="docs/assets/huella-serena-readme.svg" width="100%" alt="Huella Serena: un lugar seguro, un nuevo comienzo. Portada ilustrada con huella animada." />

<br/>

**Cuidar también es crear oportunidades.** 🐾

Una experiencia web para conectar **bienestar animal, adopciones responsables y ayuda comunitaria**.

<br/>

<a href="https://github.com/Developer-vic1/Refugio/commits/develop"><img alt="Último commit de develop" src="https://img.shields.io/github/last-commit/Developer-vic1/Refugio/develop?style=flat-square&label=Actualizado&color=17463D" /></a>
<a href="https://github.com/Developer-vic1/Refugio/issues"><img alt="Issues abiertos" src="https://img.shields.io/github/issues/Developer-vic1/Refugio?style=flat-square&label=Issues&color=BD7554" /></a>
<a href="https://github.com/Developer-vic1/Refugio/stargazers"><img alt="Estrellas del repositorio" src="https://img.shields.io/github/stars/Developer-vic1/Refugio?style=flat-square&label=Estrellas&color=17463D" /></a>

<br/>

<a href="#-descubre-huella-serena">Descubrir</a> ·
<a href="#-arranque-rápido">Instalar</a> ·
<a href="docs/RUTAS_Y_MODULOS.md">Rutas</a> ·
<a href="docs/progress/EJECUCION_V2.md">Progreso</a> ·
<a href="https://github.com/Developer-vic1/Refugio/issues">Participar</a>

</div>

---

## 🌿 Descubre Huella Serena

**Huella Serena** es una iniciativa en formación y una plataforma web para presentar animales, compartir historias y coordinar solicitudes de adopción, aportes y voluntariado. Un panel privado organiza su gestión.

| 🐶 **Explora** | 💚 **Colabora** | 🛡️ **Gestiona** |
| :---: | :---: | :---: |
| Catálogo, fichas e historias | Campañas, donaciones y voluntariado | Animales, solicitudes y publicaciones |
| [Ver rutas públicas](docs/RUTAS_Y_MODULOS.md) | [Ver módulos](docs/RUTAS_Y_MODULOS.md) | [Ver arquitectura](docs/ARQUITECTURA.md) |

<div align="center">

<img src="https://img.shields.io/badge/PHP-8.2%2B-17463D?style=flat-square&logo=php&logoColor=white" alt="PHP 8.2 o superior"/>
<img src="https://img.shields.io/badge/MySQL-8%20%2F%20MariaDB-17463D?style=flat-square&logo=mysql&logoColor=white" alt="MySQL o MariaDB"/>
<img src="https://img.shields.io/badge/Tailwind-v4-17463D?style=flat-square&logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4"/>
<img src="https://img.shields.io/badge/Arquitectura-MVC-D39A55?style=flat-square" alt="Arquitectura MVC"/>
<img src="https://img.shields.io/badge/Entorno-Laragon-D39A55?style=flat-square" alt="Laragon"/>

</div>

<details>
<summary><strong>🎨 Ver el estilo visual del proyecto</strong></summary>

<br/>
<div align="center">
  <img src="public/assets/img/inicio-paseo-v2.webp" width="800" alt="Ilustración editorial del inicio de Huella Serena: un perro paseando por el campo." />
  <p><sub>Imagen editorial generada para el diseño; no documenta un rescate real.</sub></p>
</div>

Diseño **Bosque y Hogar**, paleta cálida, animaciones accesibles, transiciones y modo de movimiento reducido. [Explorar el motor visual](docs/modules/MOTION_ENGINE.md).

</details>

## ⚡ Arranque rápido

<details open>
<summary><strong>🖥️ Laragon · Windows</strong></summary>

1. Instala PHP **8.2+**, Composer, Node.js y MySQL; prepara la base existente **<code>refugio</code>** (22 tablas y 3 vistas).
2. Copia <code>.env.example</code> como <code>.env</code> y configura la conexión local.
3. En la carpeta del proyecto, ejecuta:

   \`\`\`powershell
   composer install
   npm install
   npm run build
   php start
   \`\`\`

También puedes abrir **<code>Iniciar Refugio.cmd</code>** en Windows. Si utilizas Apache de Laragon, apunta el VirtualHost a <code>public/</code>, nunca a la raíz.

</details>

<details>
<summary><strong>🧩 ¿Cómo está organizado?</strong></summary>

\`\`\`text
public/index.php → Router → Controller → Service/Repository → MySQL
                                ↓
                       Vista PHP + Tailwind + JS
\`\`\`

**Código:** <code>app/</code> · **Rutas:** <code>routes/</code> · **Estilos y movimiento:** <code>resources/</code> · **Archivos protegidos:** <code>storage/</code>.

[Arquitectura y carpetas](docs/ARQUITECTURA.md) · [Seguridad](docs/SEGURIDAD_Y_PUBLICACION.md)

</details>

## 🌱 Estado del proyecto

**V2 en consolidación.** El portal público, los componentes visuales y varias operaciones administrativas ya están implementados. **Aún no está listo para producción**: faltan procesos completos de adopción, inventario, finanzas, permisos y pruebas integrales.

Los animales, cifras y comprobantes deben verificarse antes de publicarse como información real.

🔀 **GitFlow:** <code>feature/*</code> → <code>develop</code> → <code>main</code> tras revisión.

<div align="center">
  <a href="docs/progress/EJECUCION_V2.md"><strong>Ver avance de V2</strong></a> ·
  <a href="https://github.com/Developer-vic1/Refugio/pulls"><strong>Pull requests</strong></a> ·
  <a href="https://github.com/Developer-vic1/Refugio/issues/new"><strong>Proponer una mejora</strong></a>

  <p><sub>Desarrollado con propósito por <a href="https://github.com/Developer-vic1">@Developer-vic1</a> · 🐾 Cada huella importa.</sub></p>
</div>
