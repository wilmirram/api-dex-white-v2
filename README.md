# API DEX WHITE V2.0
...

---
### CONFIGURANDO O PROJETO
Após clonar o projeto, execute o seguinte comando:
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```
3. Suba o seu projeto com o comando `./vendor/bin/sail up -d`
4. Execute o comando `./vendor/bin/sail art key:generate` para criar sua nova chave de aplicação.
5. Acesse o browser em `http://laravel.test` para conferir o resultado.

*OBS.:* Verifique se no seu arquivo `hosts` existe o alias para `127.0.0.1 laravel.test`.

---
