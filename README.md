[![version][versao-badge]][CHANGELOG] [![license][licenca-badge]][LICENSE]

### Apresentação

Esta extensão foi desenvolvida no formato OCMOD, e tem o objetivo de habilitar na administração do OpenCart funcionalidades para criar, editar e gerenciar arquivos XML no formato OCMOD, além de possuir recursos úteis que são utilizados em atividades rotineiras durante o processo de implantação e ajuste da loja.

**As seguintes funcionalidades serão adicionadas no menu Extensões→Modificações:**

- Criar arquivos XML no formato OCMOD.
- Editar arquivos XML no formato OCMOD.
- Limpar o cache de dados gerados pela loja.
- Limpar o cache de imagens geradas pela loja.
- Limpar o cache do twig/sass gerados pela loja.
- Fazer download de qualquer arquivo XML no formato OCMOD.
- Validar o preenchimento das tags necessárias do XML no formato OCMOD.
- Validar todos os arquivos XML no formato OCMOD e gera log de erros amigável.
- Visualizar uma lista com todos os arquivos que foram modificados por OCMOD.
- Comparar diferenças entre o arquivo original e o arquivo modificado utilizando o editor.

### Instalação

 1. Acesse o link: https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=22015.
 2. Localize a extensão compatível com sua versão do OpenCart, e faça o download do arquivo "ocmod-editor.ocmod.zip".
 3. Na administração da loja acesse o menu **Extensões→Instalador** (Extensions→Installer), clique no botão **Upload** e selecione o arquivo 'ocmod-editor.ocmod.zip', e aguarde a conclusão da instalação automática.
 4. Após a instalação, acesse o menu **Extensões→Modificações** (Extensions→Modifications) e clique 2x no botão **Atualizar** (Refresh), para que a modificação instalada seja adicionada na loja, lembrando que não é o botão "**Atualizar**" do navegador, e sim o botão "**Atualizar**" na cor azul ao lado do botão laranja e vermelho na tela do próprio OpenCart.
 5. No OpenCart 3, vá na página principal do painel de controle da administração da loja, abaixo do botão "**Sair**", você verá um botão na cor azul com o desenho de uma engrenagem branca dentro dele, clique neste botão, e no popup que vai abrir, clique nos dois botões na cor laranja que estão dentro da coluna "**Ação**" para atualizar o cache do tema.

### Desinstalação

Para desinstalar a modificação, na administração da loja, acesse o menu **Extensões→Modificações** (Extensions→Modifications),  localize e selecione a modificação com o nome '**OCMOD Editor for OpenCart**', depois clique no botão **Excluir** (Delete), e depois no 2x botão **Atualizar** (Refresh).

### Atualização

Acesse a administração da loja e execute o procedimento de Desinstalação, depois execute o procedimento de Instalação.

### Dúvidas

O OCMOD (OpenCart Modification) é nativo do OpenCart, ou seja, não é necessário instalar nenhum complemento no OpenCart para utilizar modificações ou extensões no formato OCMOD, para mais informações sobre o OCMOD, segue o link para mais informações:

https://github.com/opencart/opencart/wiki/Modification-System

[versao-badge]: https://img.shields.io/badge/versão-3.2.0-blue.svg
[CHANGELOG]: ./CHANGELOG.md
[licenca-badge]: https://img.shields.io/badge/licença-GPLv3-blue.svg
[LICENSE]: ./LICENSE
