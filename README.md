# Running locally
## Requirements
- PHP 8.5
- extensions: mbstring, sockets
- Composer
- Node.js 24
- npm

### Setup
Websocket server
```sh
composer -d back install
```

Dashboard
```sh
npm --prefix front install
```

### Running the application

Task 1
```sh
composer -d back run:task1
```

Websocket server
```sh
composer -d back run:server
```

Dashboard
```sh
npm --prefix front run dev
```

Visit
http://localhost:5173/

# Running in Docker
## Requirements
- Docker
- Docker compose

Running the application
```sh
docker compose up -d
```

Running Task 1
```sh
docker compose exec -it backend php bin/task1.php
```

Visit
http://localhost:5173/