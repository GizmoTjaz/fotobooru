# Setup

**!** The project is pre-configured to run in production mode.

**!** Instructions assume $PWD is `./code/`.

## Dev (not recommended, cookies don't transfer)

- Requires changing DB hostname to `"sql"` in `fotobooru/models/DBInit.php`
- Requires changing of API endpoint in `fotobooru/frontend/src/utils/constants.ts` to `"http://localhost:41062/api"`
- Requires manual DB import on http://localhost:41062/phpmyadmin

```sh
docker run --name fotobooru-dev -p 41062:80 -d -v "$(PWD)/fotobooru":/opt/lampp/htdocs tomsik68/xampp:8
```

Website is now available at `http://localhost:41062/`.

## Production (recommended)

Optionally rebuild frontend:
```sh
cd ./fotobooru/frontend
npm i && npm build
```

Run containers:
```sh
docker compose up -d
docker exec -i db mariadb -uroot < ./fotobooru/sql/structure.sql
```
**NOTE:** Make sure others have `rwx` permissions in `./fotobooru/uploads/**` (`chmod o=rwx -R ./fotobooru/uploads/`).

Website is now available at `http://localhost:8008/`.

## Troubleshooting

### Emojis break replies

https://docs.nextcloud.com/server/stable/admin_manual/configuration_database/mysql_4byte_support.html

```sh
docker exec -it db mariadb -uroot
> ALTER DATABASE booru CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
docker compose restart db
```
