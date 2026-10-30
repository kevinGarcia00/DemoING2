# API REST PHP + MySQL: On-Premise y Cloud

El mismo código se ejecuta en local y en Render. Cada instancia usa sus propias variables de entorno: la base MySQL local y la de Aiven son independientes y **no se sincronizan automáticamente**.

## Requisitos

- PHP 8.3 o compatible, con `mysqli` y `mysqlnd` (incluidos en la imagen Docker del proyecto).
- MySQL local para On-Premise.
- Una base de datos MySQL en Aiven para Cloud.
- Cuenta GitHub y servicio web en Render.

## Crear el esquema

Crea o selecciona la base de datos MySQL y ejecuta `database/schema.sql` contra ella. La tabla usa `idUsuario` como clave autoincremental.

## Ejecución On-Premise

1. Asegúrate de que MySQL local está activo y crea la base `demoing2`.
2. Desde `backEnd`, copia `.env.example` como `.env` y completa tus credenciales locales.
3. En Bash/Zsh carga esas variables y arranca el servidor PHP integrado:

   ```sh
   set -a
   . ./.env
   set +a
   php -S 127.0.0.1:8000 router.php
   ```

4. Ejecuta el esquema localmente con el cliente MariaDB, por ejemplo: `mariadb -u root -p demoing2 < database/schema.sql`.

`.env` es solo para comodidad local; PHP no lo carga automáticamente. Está excluido de Git para evitar publicar credenciales.

## Despliegue Cloud: Aiven + Render + GitHub

1. En Aiven, crea un servicio MySQL y una base de datos. Desde la raíz del proyecto, ejecuta el esquema con el cliente MariaDB y el certificado TLS descargado de Aiven:

    ```sh
    mariadb \
       --ssl-ca="$HOME/Downloads/ca.pem" \
       --ssl-verify-server-cert \
       -h HOST_AIVEN \
       -P PUERTO_AIVEN \
       -u USUARIO_AIVEN \
       -p \
       -D NOMBRE_BASE \
       < backEnd/database/schema.sql
    ```

    Reemplaza los valores de ejemplo por los datos de conexión de Aiven. Aunque el servidor de Aiven es MySQL, el cliente `mariadb` puede conectarse a él. Cuando solicite la contraseña, ingrésala en la terminal.

    Para comprobar que la tabla se creó, ejecuta esta consulta con los mismos datos:

    ```sh
    mariadb \
       --ssl-ca="$HOME/Downloads/ca.pem" \
       --ssl-verify-server-cert \
       -h HOST_AIVEN \
       -P PUERTO_AIVEN \
       -u USUARIO_AIVEN \
       -p \
       -D NOMBRE_BASE \
       -e "SHOW TABLES; DESCRIBE usuarios;"
    ```
2. Obtén de Aiven el host, puerto, nombre de base, usuario, contraseña y certificado CA TLS.
3. Sube el proyecto a un repositorio GitHub. Si aún no hay repositorio Git configurado en la carpeta, desde la raíz del proyecto inicialízalo y enlázalo (reemplaza el URL por el de tu repositorio):

   ```sh
   git init
   git add .
   git commit -m "Preparar API PHP para Render"
   git branch -M main
   git remote add origin https://github.com/USUARIO/REPOSITORIO.git
   git push -u origin main
   ```

   No subas `.env`, contraseñas ni claves/certificados privados.
4. En Render, crea un Blueprint desde ese repositorio y usa `render.yaml` (o crea un Web Service Docker con raíz `backEnd`). Render construye la imagen desde `backEnd/Dockerfile`.
5. En la configuración del servicio Render, completa `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` y `DB_PASSWORD` con los datos de Aiven. El Blueprint activa `DB_SSL=true`.
6. Añade el certificado CA de Aiven en Render como Secret File, por ejemplo `aiven-ca.pem`, y define `DB_SSL_CA=/etc/secrets/aiven-ca.pem`. El código valida el certificado cuando se proporciona.
7. Confirma que Aiven permita conexiones entrantes desde Render. Render desplegará automáticamente los siguientes cambios que se envíen a la rama conectada de GitHub.

El puerto HTTP de Render se toma de `PORT`. La ruta de health check es `/api/health`. Para local, las variables apuntan al MySQL de tu equipo; para Render, apuntan a Aiven. No configures la base local como host de Cloud, ya que no suele ser accesible desde Render.

## Variables de entorno

| Variable | Uso | Local típico |
| --- | --- | --- |
| `DB_HOST` | Host MySQL | `127.0.0.1` |
| `DB_PORT` | Puerto MySQL | `3306` |
| `DB_NAME` | Nombre de la base | `demoing2` |
| `DB_USER` | Usuario MySQL | `root` |
| `DB_PASSWORD` | Contraseña | La configurada localmente |
| `DB_CHARSET` | Charset | `utf8mb4` |
| `DB_SSL` | Activar TLS | `false` local; `true` para Aiven |
| `DB_SSL_CA` | Ruta al certificado CA | No se usa localmente |

## Endpoints

| Método | Ruta | Entrada JSON |
| --- | --- | --- |
| `POST` | `/api/altaUsuario` | `nombre`, `apellido`, `edad`, `email` |
| `PUT` | `/api/modificarUsuario` | `idUsuario`, `nombre`, `apellido`, `edad`, `email` |
| `DELETE` | `/api/eliminarUsuario` | `idUsuario` |
| `GET` | `/api/listadoUsuarios` | — |
| `GET` | `/api/health` | — |