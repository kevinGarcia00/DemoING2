# DemoING2

## Ejecución local

1. Inicia MySQL y asegúrate de que la base de datos `demoing2` y la tabla `usuarios` ya existan.
2. Completa las credenciales de MySQL local directamente en `backEnd/config/config.php`.
3. Desde la carpeta `backEnd`, inicia el servidor:

	```sh
	php -S 127.0.0.1:8000 router.php
	```

La API estará disponible en `http://127.0.0.1:8000` y ofrece estos endpoints:

| Método | Ruta | Entrada |
| --- | --- | --- |
| `POST` | `/api/altaUsuario` | `nombre`, `apellido`, `edad`, `email` |
| `PUT` | `/api/modificarUsuario` | `idUsuario`, `nombre`, `apellido`, `edad`, `email` |
| `DELETE` | `/api/eliminarUsuario` | `idUsuario` |
| `GET` | `/api/listadoUsuarios` | Sin body |


### Funcionalidad

#### Render

Ejeuta la aplicacion en la nube y recibe solicitudes PHP.

#### Aiven

Ejecuta MySql y almacena los usuarios del sistema.


## Ejecución en la nube (Render)

Render es una plataforma para desplegar y ejecutar aplicaciones en la nube. En este proyecto se utiliza para publicar la API PHP: Render obtiene el código desde GitHub, construye el contenedor usando `backEnd/Dockerfile` y mantiene el servidor disponible en una URL pública. Render aloja la API, pero no la base de datos; la base de datos MySQL está en Aiven.

1. Ingresar a la interfaz de dashboard https://dashboard.render.com/.
2. Crear un proyecto.
3. Agregar servicio de tipo "Web Service".
4. Conectar el repositorio GitHub
5. Seleccionar el repositorio
6. Deployar servicio

### Base de datos en Aiven

Aiven es un proveedor de servicios administrados en la nube. En este proyecto se utiliza su servicio de MySQL para guardar y consultar los usuarios; Render aloja la API y Aiven aloja la base de datos.

Antes de probar endpoints que consultan o modifican usuarios, asegúrate de que el servicio MySQL de Aiven esté iniciado y su estado sea `Running` o `Available`. Si aparece como `Rebuilding`, espera a que termine. Si la base de datos está detenida, la API puede seguir levantada en Render, pero las operaciones que necesitan MySQL fallarán.

La API estará disponible en **https://demoing2.onrender.com** y ofrece estos endpoints:

| Método | Ruta | Entrada |
| --- | --- | --- |
| `POST` | `/api/altaUsuario` | `nombre`, `apellido`, `edad`, `email` |
| `PUT` | `/api/modificarUsuario` | `idUsuario`, `nombre`, `apellido`, `edad`, `email` |
| `DELETE` | `/api/eliminarUsuario` | `idUsuario` |
| `GET` | `/api/listadoUsuarios` | Sin body |



