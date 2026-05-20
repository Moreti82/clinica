# Portfólio Angular

Landing page feita em Angular para divulgar serviços de desenvolvimento de software, apps Android, web apps e desktop.

## Publicar com Termux (Android)

Agora que seu repositório `protifolio-angular` já existe, rode estes comandos no Termux:

```bash
# 1) Entrar na pasta do projeto local
cd ~/protifolio-angular

# 2) Verificar se os arquivos do Angular estão aí
ls

# 3) Configurar identidade do git (uma vez só)
git config --global user.name "Seu Nome"
git config --global user.email "seu-email@exemplo.com"

# 4) Garantir branch main
git branch -M main

# 5) Apontar origin para o repositório correto
git remote remove origin 2>/dev/null || true
git remote add origin https://github.com/Moreti82/protifolio-angular.git

# 6) Commitar e enviar
git add .
git commit -m "Initial commit - portfolio angular"
git push -u origin main
```

Quando pedir autenticação:

- **Username**: `Moreti82`
- **Password**: cole seu **PAT (Personal Access Token)** do GitHub (não a senha da conta).

> Se a senha não aparecer ao digitar/colar, é normal no terminal.

## Checklist rápido (se der erro)

```bash
cd ~/protifolio-angular
pwd
git status
git branch
git remote -v
git log --oneline -n 3
```

### Erro comum: `src refspec main does not match any`

Significa que ainda não existe commit no branch atual.

Faça:

```bash
git add .
git commit -m "Initial commit"
git branch -M main
git push -u origin main
```

### Erro comum: `remote: Repository not found`

Normalmente é URL errada ou nome diferente do repositório.

Confirme que a URL está exatamente assim:

```bash
git remote set-url origin https://github.com/Moreti82/protifolio-angular.git
```

Valide também no navegador: `https://github.com/Moreti82/protifolio-angular`.

## Deploy na Vercel

Depois do push no GitHub:

1. Acesse a Vercel e clique em **Add New Project**.
2. Selecione `protifolio-angular`.
3. Configure (ou confirme):
   - **Framework Preset**: Other
   - **Build Command**: `npm run build`
   - **Output Directory**: `dist/portfolio-angular/browser`
4. Clique em **Deploy**.

O arquivo `vercel.json` já foi incluído para ajudar nessa configuração.
