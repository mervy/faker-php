# faker-php

Um sistema completo para gerar dados fake para uso em projetos

## Funcionalidades incluídas:

1. **Formulário para inserir quantidade de registros**
2. **Mostrar apenas 5 registros na tabela por padrão e um json com 5 registros**
3. **Armazena todos os dados pedidos, ex. 100 previamente**
4. **Gera o JSON completo com todos os registros e disponibiliza botão para download**
5. **Limitação de registros para evitar problemas de memória**

## Como usar:

1. Acesse a página
2. Digite o número de registros desejados (ex: 1000)
3. Clique em "Gerar Dados"
4. Os dados aparecem na tabela (primeiros 5 registros)
5. O JSON também com 5 registros é exibido abaixo
6. Use o botão de download para salvar o arquivo JSON

Para novos dados, consulte o [site oficial do Faker PHP](https://fakerphp.org/) e edite manualmente o código.

### Ideias de tabelas

Para um blog moderno, você vai precisar de tabelas que cubram usuários, conteúdo, categorias, interações e metadados. Veja um modelo completo, pensado para escalabilidade e boas práticas:

#### users – Usuários do sistema
```sql
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'author',
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);
```

#### posts – Artigos do blog
```sql
CREATE TABLE posts (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT,
    cover_image VARCHAR(255),
    status VARCHAR(20) DEFAULT 'draft',
    published_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);
```

#### categories – Categorias dos posts
```sql
CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);
```

#### post_categories – Relacionamento posts e categorias
```sql
CREATE TABLE post_categories (
    post_id INT REFERENCES posts(id) ON DELETE CASCADE,
    category_id INT REFERENCES categories(id) ON DELETE CASCADE,
    PRIMARY KEY(post_id, category_id)
);
```

#### tags – Tags para os posts
```sql
CREATE TABLE tags (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL
);
```

#### post_tags – Relacionamento posts e tags
```sql
CREATE TABLE post_tags (
    post_id INT REFERENCES posts(id) ON DELETE CASCADE,
    tag_id INT REFERENCES tags(id) ON DELETE CASCADE,
    PRIMARY KEY(post_id, tag_id)
);
```

#### comments – Comentários dos leitores
```sql
CREATE TABLE comments (
    id SERIAL PRIMARY KEY,
    post_id INT REFERENCES posts(id) ON DELETE CASCADE,
    user_name VARCHAR(100),
    user_email VARCHAR(100),
    content TEXT NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);
```

#### media – Imagens e arquivos do blog
```sql
CREATE TABLE media (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE SET NULL,
    file_name VARCHAR(255),
    file_path VARCHAR(255),
    file_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT NOW()
);
```

#### settings – Configurações gerais do blog
```sql
CREATE TABLE settings (
    key VARCHAR(50) PRIMARY KEY,
    value TEXT
);
```

> Extras opcionais:  
> - post_revisions: histórico de edições  
> - post_views: contador de visualizações  
> - notifications: avisos para admins/autores  
> - user_roles: controle granular de permissões

---

### Exemplos de INSERT e JOIN

#### Inserindo dados iniciais

```sql
-- Usuários
INSERT INTO users (username, email, password_hash, role) VALUES
('admin', 'admin@blog.com', 'hash_exemplo', 'admin'),
('jose', 'jose@blog.com', 'hash_exemplo', 'author'),
('maria', 'maria@blog.com', 'hash_exemplo', 'author');

-- Categorias
INSERT INTO categories (name, slug, description) VALUES
('Tecnologia', 'tecnologia', 'Posts sobre tecnologia'),
('Saúde', 'saude', 'Posts sobre saúde e bem-estar'),
('Viagem', 'viagem', 'Posts sobre viagens pelo mundo');

-- Tags
INSERT INTO tags (name, slug) VALUES
('PHP', 'php'),
('JavaScript', 'javascript'),
('Fitness', 'fitness'),
('Europa', 'europa');

-- Posts
INSERT INTO posts (user_id, title, slug, content, excerpt, cover_image, status, published_at) VALUES
(2, 'Aprendendo PHP', 'aprendendo-php', 'Conteúdo do post sobre PHP...', 'Resumo do post', 'php.jpg', 'published', NOW()),
(3, 'Dicas de Viagem Europa', 'dicas-viagem-europa', 'Conteúdo do post sobre Europa...', 'Resumo do post', 'europa.jpg', 'published', NOW());

-- Relacionamento Post-Categoria
INSERT INTO post_categories (post_id, category_id) VALUES
(1, 1),
(2, 3);

-- Relacionamento Post-Tag
INSERT INTO post_tags (post_id, tag_id) VALUES
(1, 1),
(1, 2),
(2, 4);

-- Comentários
INSERT INTO comments (post_id, user_name, user_email, content, status) VALUES
(1, 'Carlos', 'carlos@email.com', 'Ótimo post!', 'approved'),
(1, 'Ana', 'ana@email.com', 'Muito útil, obrigado!', 'approved'),
(2, 'Lucas', 'lucas@email.com', 'Quero visitar a Europa também!', 'pending');
```

#### Consultas com JOINs

**a) Listar posts com autor, categorias e tags**
```sql
SELECT 
    p.id,
    p.title,
    u.username AS autor,
    STRING_AGG(DISTINCT c.name, ', ') AS categorias,
    STRING_AGG(DISTINCT t.name, ', ') AS tags
FROM posts p
JOIN users u ON p.user_id = u.id
LEFT JOIN post_categories pc ON p.id = pc.post_id
LEFT JOIN categories c ON pc.category_id = c.id
LEFT JOIN post_tags pt ON p.id = pt.post_id
LEFT JOIN tags t ON pt.tag_id = t.id
WHERE p.status = 'published'
GROUP BY p.id, u.username
ORDER BY p.published_at DESC;
```

**b) Contar posts por usuário**
```sql
SELECT 
    u.username,
    COUNT(p.id) AS total_posts
FROM users u
LEFT JOIN posts p ON u.id = p.user_id
GROUP BY u.username
ORDER BY total_posts DESC;
```

**c) Listar comentários aprovados de cada post**
```sql
SELECT 
    p.title,
    c.user_name,
    c.content,
    c.created_at
FROM comments c
JOIN posts p ON c.post_id = p.id
WHERE c.status = 'approved'
ORDER BY c.created_at DESC;
```

**d) Posts por categoria com contagem de tags**
```sql
SELECT 
    cat.name AS categoria,
    p.title,
    COUNT(pt.tag_id) AS total_tags
FROM posts p
JOIN post_categories pc ON p.id = pc.post_id
JOIN categories cat ON pc.category_id = cat.id
LEFT JOIN post_tags pt ON p.id = pt.post_id
GROUP BY cat.name, p.title
ORDER BY cat.name, total_tags DESC;
```

**e) Buscar posts com uma tag específica**
```sql
SELECT 
    p.title,
    u.username AS autor,
    STRING_AGG(c.name, ', ') AS categorias
FROM posts p
JOIN users u ON p.user_id = u.id
JOIN post_tags pt ON p.id = pt.post_id
JOIN tags t ON pt.tag_id = t.id
LEFT JOIN post_categories pc ON p.id = pc.post_id
LEFT JOIN categories c ON pc.category_id = c.id
WHERE t.slug = 'php'
GROUP BY p.id, u.username;
```

> **Observações:**  
> - `STRING_AGG` é do PostgreSQL. No MySQL, use `GROUP_CONCAT`.  
> - As queries já consideram joins muitos-para-muitos e filtragem de status.  
> - Expanda para filtros por data, busca por palavras-chave, etc.

### Outro exemplo de estrutura de tabelas

#### authors – Autores
```sql
CREATE TABLE authors (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) DEFAULT NULL,
    surname VARCHAR(100) DEFAULT NULL,
    nickname VARCHAR(100) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    password VARCHAR(255) DEFAULT NULL,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
```

#### categories – Categorias
```sql
CREATE TABLE categories (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) DEFAULT NULL,
    slug VARCHAR(100) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
```

#### articles – Artigos
```sql
CREATE TABLE articles (
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) DEFAULT NULL,
    image VARCHAR(500) DEFAULT NULL,
    content LONGTEXT DEFAULT NULL,
    status TINYINT(1) DEFAULT 1,
    authors_id INT(11) DEFAULT NULL,
    categories_id INT(11) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (authors_id) REFERENCES authors(id),
    FOREIGN KEY (categories_id) REFERENCES categories(id)
);
```

#### newsletters – Assinantes de newsletter
```sql
CREATE TABLE newsletters (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    ip VARCHAR(45) DEFAULT NULL,
    status TINYINT(1) DEFAULT 1,
    registered_in TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
```

#### visitors – Visitantes dos artigos
```sql
CREATE TABLE visitors (
    id INT(11) NOT NULL AUTO_INCREMENT,
    articles_id INT(11) DEFAULT NULL,
    ip VARCHAR(45) DEFAULT NULL,
    accessed_in TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (articles_id) REFERENCES articles(id)
);
```
