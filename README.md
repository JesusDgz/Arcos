🧾 Descripción general de la aplicación
Esta es una aplicación web completa (frontend + backend) para administrar un restaurante, basada en:

Frontend: Vue 3 + Vuetify + Pinia

Backend: Laravel 12 (PHP)

Base de datos: MySQL

La aplicación permite registrar pedidos, administrarlos, gestionarlos por roles (mesero, cocinero, repartidor, admin), visualizar estadísticas y reportes, y realizar cobros desde una interfaz intuitiva.

📊 Flujo general de la aplicación
Login / Autenticación

El usuario se autentica mediante un token JWT.

El store de Pinia guarda los datos (usuario, token) en localStorage.

Panel de Administración (/admin)

Vista para el rol admin.

Puede ver estadísticas y abrir CRUDs modales:

Usuarios

Productos

Pedidos

También permite registrar productos o reportes manualmente.

Módulo Restaurante (Mesero)

Mesero selecciona productos por categoría y los agrega al pedido.

Se muestra el total.

Al enviar el pedido, se registra con estado = pendiente en la tabla pedidos.

Módulo Cocina (Cocinero)

Vista de cocina muestra los pedidos pendientes y sus productos.

Se actualiza en tiempo real con la opción de cambiar el estado a listo.

Módulo de Caja (Cobro)

Se selecciona el pedido pendiente.

Se calcula el total, se ingresa el monto de pago y se muestra el cambio.

Al confirmar, el pedido cambia su estado a entregado.

🗂️ Estructura de archivos importantes
Frontend (Vue)
bash
Copiar
Editar
frontend/
│
├── src/
│   ├── components/
│   │   └── TablesAdmin/    # UsuariosTable.vue, ProductosTable.vue, PedidosTable.vue
│   ├── views/
│   │   └── AdminView.vue
│   │   └── RestauranteView.vue
│   │   └── CobroView.vue
│   ├── stores/
│   │   └── user.js         # Almacena usuario, token y logout
│   ├── services/
│   │   └── api.js          # Axios config
│   └── router/
│       └── index.js        # Rutas protegidas según rol
Backend (Laravel)
bash
Copiar
Editar
backend/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php     # Login
│   │       ├── PedidosController.php  # Lógica de pedidos
│   │       ├── UsuariosController.php
│   │       └── ProductosController.php
│   ├── Models/
│       ├── Pedido.php
│       ├── Usuario.php
│       └── Producto.php
├── routes/
│   └── api.php              # Endpoints REST para el frontend
├
│
└── .env                     # Variables de conexión a DB y token
🛠️ Base de datos
Tablas clave:

usuarios → login y roles

productos → menú disponible

pedidos → pedidos realizados

detalle_pedido → productos de cada pedido

clientes → dirección si aplica (envío)

reportes → para análisis de datos

🔄 Conexión frontend-backend
El frontend usa axios para enviar solicitudes a rutas /api/.

El token se adjunta en el header Authorization.

Las vistas se actualizan automáticamente con ref() y watch().

✅ Roles y permisos
Admin: acceso total (CRUD, reportes, cocina, cobros).

Mesero: crea pedidos.

Cocinero: ve pedidos pendientes.

Cajero: cobra pedidos.
