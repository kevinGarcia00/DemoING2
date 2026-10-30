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