<img src='https://sigarra.up.pt/feup/pt/imagens/LogotipoSI' width="30%"/>

<div align="center">
🌍 <a href="README.md">English</a> | 🇵🇹 <a href="README.pt.md">Português</a>
</div>

<h3 align="center">Licenciatura em Engenharia Informática e Computação<br>L.EIC019 - Linguagens e Tecnologias Web<br> 2023/2024 </h3>

---
<h3 align="center"> Colaboradores &#129309 </h2>

<div align="center">

| Nome            | Número      |
|---------------- |-------------|
| Joana Carvalhal | up202306568 |
| Martim Cadilhe  | up202307833 |
| Tomás Morais    | up202303834 |

Nota : 15,1

</div>

# Relatório do Projeto Learn2Day

* [Funcionalidades](#funcionalidades) 
  * [Utilizador](#utilizador)
  * [Freelancers](#freelancers)
  * [Clientes](#clientes)
  * [Administradores](#administradores)
* [Execução](#execução)
* [Credenciais](#credenciais)

## Funcionalidades

### Utilizador
- [x] Registar uma nova conta.
- [x] Iniciar e terminar sessão.
- [x] Editar o seu perfil, incluindo nome, username, palavra-passe e email.

### Freelancers
- [x] Listar novos serviços, fornecendo detalhes como categoria, preço, tempo de entrega e descrição do serviço, juntamente com imagens ou vídeos.
- [x] Acompanhar e gerir os seus serviços oferecidos.
- [x] Responder a perguntas de clientes sobre os seus serviços e fornecer propostas personalizadas, se necessário.
- [ ] Marcar serviços como concluídos após a entrega.

### Clientes
- [x] Pesquisar serviços utilizando filtros como categoria, preço e avaliação.
- [x] Interagir com freelancers para colocar questões ou pedir encomendas personalizadas.
- [x] Contratar freelancers e avançar para o checkout (simulação do processo de pagamento).
- [x] Deixar classificações e avaliações para serviços concluídos.

### Administradores
- [x] Elevar um utilizador para administrador.
- [ ] Introduzir novas categorias de serviços e outras entidades relevantes.
- [x] Supervisionar e garantir o bom funcionamento de todo o sistema.

## Execução

```bash
sqlite3 docs/learn2day.db < docs/learn2day.sql
cd src
php -S localhost:9000
```

## Credenciais

- martim/123456 -> Conta de administrador
- testetutor/123456 -> Conta de tutor
- testestudent/123456 -> Conta de estudante
