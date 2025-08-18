# 🛒 Minha Loja

Um sistema simples de loja virtual desenvolvido em **Laravel**, com
cadastro de produtos, carrinho de compras e finalização de pedidos.

## 🚀 Tecnologias

-   [Laravel 11](https://laravel.com/)
-   [Bootstrap](https://getbootstrap.com/) (para estilização)
-   [MySQL](https://www.mysql.com/) (banco de dados)

## 📂 Funcionalidades

-   ✅ Listagem de produtos
-   ✅ Adição de produtos ao carrinho
-   ✅ Exibição do valor total e quantidade de itens no carrinho
-   ✅ Cadastro, edição e exclusão de produtos
-   ✅ Criação de pedidos

## ⚙️ Instalação

Clone o repositório:

``` bash
git clone https://github.com/seu-usuario/minha-loja.git
cd minha-loja
```

Instale as dependências:

``` bash
composer install
npm install && npm run dev
```

Crie o arquivo `.env`:

``` bash
cp .env.example .env
```

Gere a chave da aplicação:

``` bash
php artisan key:generate
```

Configure o banco de dados no `.env` e rode as migrações:

``` bash
php artisan migrate
```

Se quiser já começar com alguns produtos:

``` bash
php artisan db:seed
```

Inicie o servidor:

``` bash
php artisan serve
```

Acesse em: <http://localhost:8000>

## 🔑 Rotas principais

-   `/` → Listagem de produtos
-   `/cart` → Visualização do carrinho
-   `/products/create` → Cadastro de produto
-   `/orders` → Finalização de pedidos

## 🤝 Contribuição

Fique à vontade para abrir **issues** e **pull requests**.

------------------------------------------------------------------------

👩‍💻 Desenvolvido com ❤️ em Laravel.
