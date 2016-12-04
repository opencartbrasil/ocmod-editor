### Resumo

Esta modificação foi desenvolvida parcialmente no formato OCMod, pois acompanha arquivos extras que não alteram nenhum arquivo de core do OpenCart.

O objetivo desta modificação é permitir a criação e edição de XML no formato OCMod através da administração da loja utilizando um editor online, o que torna o processo de modifciação mais produtivo. Também é possível fazer o download do arquivo XML no formato OCMod, e limpar o cache de imagens e dados do OpenCart.

Caso deseje doar um valor para contribuir com este trabalho continuo e sempre gratuito, clique no botão abaixo:

[![alt tag](https://www.paypalobjects.com/pt_BR/BR/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7G9TR9PXS6G5J)

### Instalação

 1. Baixe a modificação no link: https://github.com/opencartbrasil/ocmod-editor/releases.
 2. Localize a versão mais atual da modificação que seja compatível com sua versão do OpenCart, e clique no link "ocmod-editor.ocmod.zip".
 3. Na administração da loja acesse o menu Extensions->Extension Installer (Extensões->Instalador).
 4. Na página do instalador, clique no botão Upload e selecione o arquivo 'ocmod-editor.ocmod.zip' (que você baixou deste repositório), e aguarde a conclusão da instalação automática.
 5. Após a instalação, acesse o menu Extensions->Modifications (Extensões->Modificações) e clique no botão Refresh (Atualizar), para que a modificação instalada seja incrementada na loja, lembrando que não é o botão "Atualizar" do navegador, e sim o botão "Atualizar" na cor azul ao lado do botão laranja e vermelho na tela do próprio OpenCart.

### Utilização

No menu Extensions->Modifications (Extensões->Modificações).

- Você poderá fazer o download do arquivo XML para OCMod, através do botão "Download".
- Você poderá editar o arquivo XML para OCMod, através do botão "Editar".
- Você poderá criar arquivos XML do tipo OCMod, através do botão "Nova modificação".
- Quando estiver editando ou criando um arquivo XML do tipo OCMod, você poderá excluir o cache de imagens através do botão "Apagar cache de imagens".
- Quando estiver editando ou criando um arquivo XML do tipo OCMod, você poderá excluir o cache de dados através do botão "Apagar cache de dados".

### Desinstalação

Para desinstalar a modificação, na administração da loja, acesse o menu Extensions->Modifications (Extensões->Modificações),  localize e selecione a modificação com o nome 'OCMod Editor', depois clique no botão Delete (Excluir), e no botão Refresh (Atualizar).

### Atualização

Acesse a administração da loja e execute o procedimento de Desinstalação, depois execute o procedimento de Instalação.

### Dúvidas

O OCMod (OpenCart Modification) é nativo do OpenCart, ou seja, não é necessário instalar nenhum complemento no OpenCart para utilizar modificações ou extensões no formato OCMod, para mais informações sobre o OCMod, segue o link:

https://github.com/opencart/opencart/wiki/Modification-System

### Os arquivos alterados virtualmente através do OCMod são:

admin/controller/extension/modification.php

admin/view/template/extension/modification.tpl

### Como contribuir

 1. Faça um Fork do projeto e edite os arquivos que desejar.
 2. Faça um Pull para que suas sugestões de melhorias sejam avaliadas e aceitas, caso aprovadas.
 3. Abra uma Inssue com sua dúvida ou sugestão.

### Licença

[GNU General Public License version 3 (GPLv3)](https://github.com/opencartbrasil/ocmod-editor/blob/master/LICENSE)
