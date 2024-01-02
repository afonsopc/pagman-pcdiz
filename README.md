# PCDiz

O PCDiz é um website de comércio eletrónico simples, implementado em PHP. É uma paródia da empresa PCDiga. O website permite aos utilizadores visualizar, adicionar, editar e apagar produtos. Além disso, os utilizadores podem fazer compras.

## Estrutura do Projeto

O projeto está estruturado da seguinte forma:

- `add.php`: Esta página permite aos utilizadores adicionar novos produtos à base de dados.
- `admin.php`: Esta página é para administradores. Permite gerir todos os produtos e utilizadores.
- `auth.php`: Esta página gere a autenticação dos utilizadores. Permite aos utilizadores fazer login e logout.
- `buy.php`: Esta página permite aos utilizadores comprar produtos. Quando um produto é comprado, é adicionado à base de dados de encomendas.
- `delete.php`: Esta página permite aos administradores apagar produtos da base de dados.
- `delete_user.php`: Esta página permite aos administradores apagar utilizadores da base de dados.
- `edit.php`: Esta página permite aos administradores editar os detalhes de um produto.
- `index.php`: Esta é a página inicial do website. Mostra todos os produtos disponíveis para compra.
- `logout.php`: Esta página permite aos utilizadores fazer logout.
- `orders.php`: Esta página mostra todas as encomendas feitas por um utilizador.
- `view.php`: Esta página mostra os detalhes de um produto específico.
- `view_user.php`: Esta página mostra os detalhes de um utilizador específico.

## Variáveis de Ambiente

- `DEFAULT_ADMIN_USERNAME`: Esta variável de ambiente é usada para definir o nome de utilizador do administrador por defeito quando o website é inicializado pela primeira vez.

## Como usar

Para usar este website, primeiro precisa de fazer login. Depois de fazer login, pode ver todos os produtos disponíveis na página inicial. Se for um administrador, pode adicionar, editar ou apagar produtos.

Para comprar um produto, clique no produto e será levado para a página de detalhes do produto. Aí, pode adicionar o produto ao seu carrinho e finalizar a compra.

## Docker

Este projeto usa Docker para facilitar a configuração do ambiente de desenvolvimento. Para iniciar o projeto, precisa de ter o Docker instalado no seu sistema. Depois de instalado, pode usar o comando `docker compose up` para iniciar o website.

## Base de Dados

A base de dados do projeto está contida na pasta `database`. O esquema da base de dados pode ser encontrado no ficheiro `schema.sql`.

### Feito por Afonso Maria Pacheco de Castro Pereira Coutinho