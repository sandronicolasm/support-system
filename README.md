# Sistema de Gestión de Tickets

Este sistema permite la gestión eficiente de tickets, asignando prioridades según reglas de negocio predefinidas, gestionando notificaciones por correo y habilitando mensajería instantánea entre clientes y personal técnico. El sistema está diseñado para ejecutarse mediante Docker para facilitar la configuración del entorno y utiliza Symfony AssetMapper para la gestión de recursos estáticos.

# Requisitos Previos

	1.	Docker y Docker Compose instalados.
	2.	Git instalado para clonar el repositorio.
	3.	Composer instalado globalmente (opcional, para ejecutar comandos directamente en el contenedor).

## Instalación

Paso 1: Clonar el Repositorio

Clona el repositorio y accede al directorio del proyecto:

git clone https://github.com/sandronicolasm/support-system.git
cd support-system

Paso 2: Configurar Variables de Entorno

Crea un archivo .env.local basado en .env y configura las credenciales:

DATABASE_URL="mysql://usuario:contraseña@db:3306/base_de_datos"

	•	DATABASE_URL: Credenciales de acceso a la base de datos MySQL dentro del contenedor.

Paso 3: Iniciar los Contenedores con Docker

Inicia los contenedores de la aplicación y la base de datos:

docker-compose up -d

Esto levantará los servicios definidos en el archivo docker-compose.yml, incluyendo:
	•	PHP/Symfony: Contenedor para la aplicación.
	•	MySQL: Base de datos para almacenamiento de tickets.
	•	MailHog: Herramienta para pruebas de correos.
	•	Mercure: Hub para notificaciones en tiempo real de tickets enviados por el cliente.

Paso 4: Configuración de la Base de Datos

Ejecuta las siguientes migraciones desde el contenedor PHP para crear las tablas necesarias:

docker compose exec php bin/console doctrine:database:create
docker compose exec php bin/console doctrine:migrations:migrate
docker compose exec php bin/console messenger:setup-transports

Nota: Reemplaza app-container con el nombre real del contenedor definido en docker-compose.yml.

Paso 5: Gestionar Recursos con AssetMapper

El sistema utiliza Symfony AssetMapper para la gestión de archivos estáticos.
	1.	Publica los assets necesarios:

docker compose exec php bin/console asset-map:compile

Características del Sistema

Servicios

Asignación de Prioridad a Tickets
    •	Ubicación: src/Service/TicketPriorityAssigner.php
    •	Criterios:
        •	Nivel de urgencia.
        •	Tipo de problema.
        •	Fecha límite.
        ## Criterios Predefinidos

        1.	Urgencia:
            •	Si el nivel de urgencia del ticket es “alta”, la prioridad será Alta.
            •	Si la urgencia es “media”, la prioridad será Media.
            •	Si no se especifica o es “baja”, la prioridad será Baja.
        2.	Tipo de Problema:
            •	Si el problema es técnico, la prioridad será Alta.
        3.	Proximidad a la Fecha Límite:
            •	Si el ticket tiene una fecha límite establecida dentro de las próximas 24 horas, la prioridad será Alta. (TODO: agregar fecha limite en formato datetime en el form)

        ## Reglas Finales

            •	Se evaluarán los criterios en el orden establecido. Si un criterio cumple las condiciones para asignar Alta, no se evaluarán los siguientes.
            •	Si ningún criterio cumple para Alta o Media, la prioridad será Baja.
Notificaciones por Correo
	    •	Ubicación: src/MessageHandler/EmailNotificationHandler.php
	    •	Implementación con Symfony Messenger para el envío de notificaciones asincrónicas de la actualizacion de estados de tickets desde el listado de tickets.

Mensajería Instantánea

Permite a los clientes enviar mensajes relacionados con tickets abiertos. La funcionalidad utiliza Stimulus en el frontend para gestionar los eventos de actualización de estado en tiempo real.

Entidades Principales

	1.	Ticket
	•	Atributos: id, title, description, priority, status, deadline, createdAt, status, urgency, problem_type.

	2.	TicketStatus
	•	Atributos: id, name. // utilizado para presetear los status de tickets

    3.	ProblemType
	•	Atributos: id, name. // utilizado para presetear los tipos de problemas

	4.	Urgency
	•	Atributos: id, name. // utilizado para presetear la urgencia que establece el solicitante

Configuración del .env.local

Asegúrate de configurar estas variables para adaptarlas a tu entorno de desarrollo:

MAILER_DSN=smtp://mailhog:1025
MESSENGER_TRANSPORT_DSN=doctrine://default
APP_ENV=dev
MERCURE_URL=http://mercure/.well-known/mercure
MERCURE_PUBLIC_URL=http://localhost:3000/.well-known/mercure
MERCURE_JWT_SECRET='!ChangeThisMercureHubJWTSecretKey!'


	•	MAILER_DSN: Usa MailHog para pruebas locales de correos.
	•	MESSENGER_TRANSPORT_DSN: Configuración para usar Doctrine como transporte.
    •   MERCURE_*: Variables de Mercure para tener un hub para servir los mensajes dinamicos

Ejecutar Tareas con Symfony

Procesar Mensajes

Para consumir los mensajes en cola:

docker compose exec php bin/console messenger:consume async

Depuración

Para inspeccionar el profiler de Symfony:

APP_DEBUG=1

Pruebas Locales de Email

El sistema utiliza MailHog para capturar correos enviados. Accede a la interfaz de MailHog desde tu navegador:
http://localhost:8025

Se recibirán los mensajes de cambio de status de ticket al cliente. 