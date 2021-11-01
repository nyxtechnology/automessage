### Configuração do Ambiente de Produção
* Criar crontab com codigo abaixo:<br/>
<b>É muito importante que o <u>PATH</u> esteja correto!</b><br>
Você pode acessar esse [link](https://askubuntu.com/questions/23009/why-crontab-scripts-are-not-working) para saber como pegar o <b><u> PATH </u></b> correto do sistema atual.
```bash
AUTOMESSAGE=/home/automessage/automessage
PATH=/root/.nvm/versions/node/v8.10.0/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games:/snap/bin
* * * * * /root/automessage/docker/scripts/laravelcron.sh
```
* Dar permissão para executar o script
```bash
chmod +x /root/automessage/docker/scripts/laravelcron.sh
```

* Certifique-se de criar os arquivos de configuração na pasta <b>config</b>. Para isso, use como base os arquivos listados abaixo, renomeando-os excluindo a palavra <b>'example'</b> e inserindo os valores corretos.
```
eventsMapConfig.example.json
mailchimp.example.php
mailgun.example.php
mandrill.example.php
```

* Acesse o container usando comando `make in` do respectivo ambiente e rode o `composer install`.

* Certifique-se que exista o arquivo `.env` se não, crie um usando como base o `.env.example`

* Leitura da fila `rabbitmq` é preciso iniciar o trabalho de leitura com o seguinte comando:
```
make in
php artisan queue:work
```
### Para rodar o projeto

* Crie um banco de dados e adicione as informações dele no arquivo `.env`. 
* Rode o comando `php artisan migrate` para criar as tables no seu banco de dados.

### Configurando o arquivo .env

* Em `MAIL_DRIVER` adicione o tipo do seu provedor de email (normalemte é IMAP ou SMTP).
* Em `MAIL_HOST` adicione o server do seu provedor de email (ex: smtp.gmail.com).
* Em `MAIL_PORT` adicione a porta do seu provedor de email(ex: 587 ou 465).
* Em `MAIL_USERNAME` adicione o endereço de email que pretende utilizar.
* Em `MAIL_PASSWORD` adicione a senha do email.
* Em `MAIL_ENCRYPTION` adicione o tipo de criptografia do seu provedor de email (ex: ssl).
* Em `MAIL_FROM_ADDRESS` adicione o seu endereço de email.
* Em `MAIL_FROM_NAME` adicione o nome que será utilizado para identificar o email (ex: seu nome ou nome da empresa)
