### Usuarios 1
- id INT PRIMARY KEY NOT NULL
- nombre VARCHAR(50) NOT NULL
- correo VARCHAR(50) NOT NULL


### Especie 2
- id INT PRIMARY KEY NOT NULL
- nombre VARCHAR(50) NOT NULL
- descripcion  VARCHAR(50) NOT NULL

### Raza 3
- id INT PRIMARY KEY NOT NULL
- id_especie
- nombre VARCHAR(50) NOT NULL
- descripcion  VARCHAR(50) NOT NULL

### colores 4
- id INT PRIMARY KEY NOT NULL
- hexcolor VARCHAR(10) NULL
- nombre VARCHAR(50) NOT NULL

### estado_mascota 5
- id INT PRIMARY KEY NOT NULL
- nombre VARCHAR(50) NOT NULL

### refugios 6
- id INT PRIMARY KEY NOT NULL
- nombre VARCHAR(50) NOT NULL
- direccion VARCHAR(150) NOT NULL
-  telefono VARCHAR(150) NULL
-  correo VARCHAR(150) NULL


### Mascota 7
- id INT PRIMARY KEY NOT NULL
- nombre VARCHAR(50) NOT NULL
- id_especie NOT NULL
- id_raza NOT NULL
- Sexo CHAR(1) NOT NULL
- fecha_nacimiento DATE NOT NULL
- edad_estimada
- color_id FK
-  peso_kg DECIMAL (5,2)
-  observaciones TEXT
-   id_estado_salud FK
-   fecha_registro
-   estado ENUM ('activo', 'inactivo')
-   id_refugio

### Viviendas 8
- id PRIMARY KET NOT NULL
- nombre VARCHAR(50)
- limite_animales INT (3) NOT NULL

### Personas 9
- id INT PRIMARY KEY NOT NULL
- nombre VARCHAR(50) NOT NULL
- tipo_documento ENUM('DPI', 'PASAPORTE')  
- no_documento VARCHAR(25)  NOT NULL
- correo VARCHAR(150) NOT NULL
- mascotas_actuales INT NOT NULL
- id_vivienda FK 
- ingresos INT NOT NULL

### Estados_adopciones 10
- id INT PRIMARY KEY NOT NULL
- nombre

### Adopciones 11
- id INT PRIMARY KEY NOT NULL
- id_mascota FK 
- id_persona FK
- fecha_solicitud DATE NOT NULL
- fecha_aprobacion DATE NULL
- fecha_entrega DATE NULL
- observaciones TEXT
- documento
- id_estado_adopcion   FK
- id_usuario

### seguimiento_adopcion 12
- id INT PRIMARY KEY NOT NULL
- id_adopcion FK
- fecha_seguimiento DATE NOT NULL
- observaciones TEXT NOT NULL
- id_usuario 


### Roles 13
- id INT PRIMARY KEY NOT NULL
- nombre VARCHAR(50)
- descripcion VARCHAR (100)

### Permiso 14
- id INT PRIMARY KEY NOT NULL
- nombre VARCHAR(50)
- descripcion VARCHAR (100)

### rol_permiso 15
- rol_id FK
- permiso_id FK

### usuario_rol  16
- usuario_id FK
- permiso_id FK

### mascota_historial 17
- id  INT PRIMARY KEY  NOT NULL 
- id_mascota FK
- id_estado_mascota FK
- observaciones TEXT NOT NULL
- fecha_historial DATE NOT NULL
- id_usuario FK 

### tipo_tratamientos 18
- id INT PRIMARY KEY NOT NULL
- nombre

### tratamientos 19
- nombre
- requiere_veterinario TINYINT NOT NULL
- costo 
- tipo_tratamiento

### mascota_tratamientos 20
- id_mascota FK
- tratamiento FK
- indicaciones TEXT NOT NULL
- fecha_tratamiento TEXT NOT NULL

### citas 21
- id_mascota FK
- fecha_cita DATE NOT NULL
- observaciones TEXT
- estado_cita ENUM ('pendiente', 'realizada', 'incumplida')

### donantes 22
- id PRIMARY KEY NOT NULL 
- nombre

### aportacion 23
- id PRIMARY KEY NOT NULL
- id_donante
- monto
- tipo_aporte ENUM('efefctivo', 'transferencia', 'especie')
- id_mascota FK NULLABLE

### Rescates 24
- id PRIMARY KEY NOT NULL
- id_usuario FK
- id_mascota FK
- observaciones TEXT
- estado_mascota FK

### contratos  25
- id PRIMARY KEY NOT NULL
- id_usuario
- nombre
- contrato