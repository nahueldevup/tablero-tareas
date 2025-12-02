#Tablero De Tareas En Tiempo Real

>  **Trabajo Practico Tiempo real** - PROGRAMACIÓN IV 
>  **Comisión 2.2**
>  **Integrantes: Jennifer Coronel - Nahuel Espinola**

---
## Tabla de Contenidos
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Tecnologías](#tecnologías)
---
## Requisitos

- **PHP**: 8.3 o superior
- **MySQL**: 8.4+ / MariaDB
- **Composer**: 2.x
- **Node.js**: 22.x+ & NPM

---

## Instalación

### 1. Clonar el Repositorio

```bash
git clone https://github.com/nahueldevup/tablero-tareas.git
cd tablero-tareas
```

### 2. Instalar Dependencias PHP

```bash
composer install
npm install && npm run build
```

### 3. Configurar Variables de Entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Iniciar el Servidor

```bash
  npm run dev-all
```

Accede a: **http://localhost:8000**
---

## Tecnologías
- **Framework**: Laravel 12
- **WebSocket**:Reverb
- **ORM**: Eloquent
- **CSS** 
- **Templates**: Blade
- **Build Tool**: Vite
- **Icons**: bootstrap
- **JavaScript**:Echo.js

---
