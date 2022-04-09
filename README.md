[![license][licenca-badge]][LICENSE]

### Apresentação

Esta modificação foi desenvolvida no padrão OCMOD, e tem o objetivo de habilitar na administração do OpenCart funcionalidades para criar, editar e gerenciar arquivos XML no padrão OCMOD, além de possuir recursos úteis que são utilizados em atividades rotineiras durante o processo de implantação e ajustes da loja.

**As seguintes funcionalidades são adicionadas no menu Extensões→Modificações:**

- Criar arquivos XML no padrão OCMOD.
- Editar arquivos XML no padrão OCMOD.
- Limpar o cache de dados gerados pela loja.
- Limpar o cache de imagens geradas pela loja.
- Limpar o cache do twig/sass gerados pela loja.
- Fazer download de qualquer arquivo XML no padrão OCMOD.
- Pesquisar trechos de código em arquivos XML no padrão OCMOD.
- Validar o preenchimento das tags obrigatórias no XML em padrão OCMOD.
- Validar todos os arquivos XML no padrão OCMOD e gerar log de erros amigável.
- Visualizar em qual arquivo ocmod.zip a modificação OCMOD está vinculada.
- Visualizar uma lista com todos os arquivos que foram modificados por OCMOD.
- Comparar diferenças entre o arquivo original e o arquivo modificado utilizando o editor.
- Editar e salvar arquivos de cache modificados por OCMOD para identificar problemas de conflito entre modificações.

### Instalação

 1. Acesse a URL: https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=22015.
 2. Localize a extensão compatível com sua versão do OpenCart e faça o download do arquivo "ocmod-editor.ocmod.zip".
 3. Na administração da sua loja, vá ao menu **Extensões→Instalador** (Extensions→Installer), clique no botão **Upload**, selecione o arquivo '**ocmod-editor.ocmod.zip**' e aguarde a conclusão da instalação automática.
 4. Após a instalação, vá ao menu **Extensões→Modificações** (Extensions→Modifications) e clique 2x no botão **Atualizar** (Refresh), para que a modificação instalada seja incrementada na loja, lembrando que não é o botão "**Atualizar**" do navegador, e sim o botão "**Atualizar**" na cor azul ao lado do botão laranja e vermelho na tela do próprio OpenCart.
 5. No OpenCart 3, vá na página principal do painel de controle da administração da loja, abaixo do botão "**Sair**", você verá um botão na cor azul com o desenho de uma engrenagem branca dentro dele, clique neste botão, e no popup que vai abrir, clique nos dois botões na cor laranja que estão dentro da coluna "**Ação**" para atualizar o cache do tema.

### Desinstalação

Para desinstalar a modificação, na administração da sua loja, vá ao menu **Extensões→Modificações** (Extensions→Modifications),  localize e selecione a modificação com o nome '**OCMOD Editor for OpenCart...**', depois clique no botão **Excluir** (Delete) e depois no 2x botão **Atualizar** (Refresh).

### Atualização

Na administração de sua loja, execute o procedimento de Desinstalação (descrito neste manual), depois execute o procedimento de Instalação (descrito neste manual).

### Dúvidas

O OCMOD (OpenCart Modification) é nativo do OpenCart, ou seja, não é necessário instalar nenhum complemento no OpenCart para utilizar modificações ou extensões no padrão OCMOD, para mais informações sobre o OCMOD, segue a URL para mais informações:

https://github.com/opencart/opencart/wiki/Modification-System

[licenca-badge]: https://img.shields.io/badge/licença-GPLv3-blue.svg
[LICENSE]: ./LICENSE
