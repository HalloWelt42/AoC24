# Docker-Symfony-Stack

Mit diesem Docker-Symfony-Stack ist es möglich, in Sekundenschnelle eine lokale Entwicklungsumgebung einzurichten. Jedes Komponente ist für die Ausführung von Symfony 6 auf eine spezielle Weise ausgewählt.

## Erste Schritte
Kopiere die `.env.dist`-Datei und bearbeite die Einträge entsprechend deiner Bedürfnisse:
```
cp .env.dist .env
```

Starte nur Docker Compose, um deine Umgebung zu starten:
```
docker-compose up
```

Nachdem der Container gebootet wurde, kannst du Composer und die Symfony CLI innerhalb des php-apache-Containers verwenden:
```
docker exec -it symfony-apache-php bash
symfony check:requirements
# Neueste Symfony-Version
composer create-project symfony/skeleton ./
# Spezifische Symfony-Version
composer create-project symfony/skeleton:"6.4.*" ./
```

## Installierte Pakete
Es laufen Container: Apache-PHP
- [Web-App](http://localhost)

## Domains für die Arbeit unter MacOS
`sudo nano /etc/hosts`
Füge die folgenden Zeilen in deine `/etc/hosts`-Datei ein:
```
# ... other stuff
# macdomains
127.0.0.1       selforg.mac
192.168.43.103  selforg.mac
```