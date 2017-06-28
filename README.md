[![version][versao-badge]][CHANGELOG] [![license][licenca-badge]][LICENSE]

### Apresentação

Esta modificação foi desenvolvida no formato OCMOD, e tem o objetivo de permitir a criação e edição de código XML no formato OCMOD através da administração da loja em um editor online, o que torna o processo mais produtivo, além de adicionar as funcionalidade de fazer download de qualquer arquivo XML OCMOD que esteja instalado no OpenCart, e limpar o cache de imagens e dados do OpenCart.

Caso deseje doar um valor para contribuir com este trabalho continuo e sempre gratuito, clique no botão abaixo:

[![alt tag](https://www.paypalobjects.com/pt_BR/BR/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7G9TR9PXS6G5J)

### Instalação

 1. Acesse o link: https://github.com/opencartbrasil/ocmod-editor/releases.
 2. Localize a versão mais atual e compatível com sua versão do OpenCart, e faça o download do arquivo "ocmod-editor.ocmod.zip".
 3. Na administração da loja acesse o menu Extensões→Instalador (Extensions→Installer).
 4. Na página do instalador, clique no botão Upload e selecione o arquivo 'ocmod-editor.ocmod.zip' (que você baixou deste repositório), e aguarde a conclusão da instalação automática.
 5. Após a instalação, acesse o menu Extensões->Modificações (Extensions->Modifications) e clique no botão Atualizar (Refresh), para que a modificação instalada seja incrementada na loja, lembrando que não é o botão "Atualizar" do navegador, e sim o botão "Atualizar" na cor azul ao lado do botão laranja e vermelho na tela do próprio OpenCart.

### Utilização

No menu Extensões→Modificações (Extensions->Modifications).

- Você poderá fazer o download do arquivo XML para OCMOD, através do botão "Download".
- Você poderá editar o arquivo XML para OCMOD, através do botão "Editar".
- Você poderá criar arquivos XML do tipo OCMOD, através do botão "Nova modificação".
- Quando estiver editando ou criando um arquivo XML do tipo OCMOD, você poderá excluir o cache de imagens através do botão "Apagar cache de imagens".
- Quando estiver editando ou criando um arquivo XML do tipo OCMOD, você poderá excluir o cache de dados através do botão "Apagar cache de dados".

### Desinstalação

Para desinstalar a modificação, na administração da loja, acesse o menu Extensões→Modificações (Extensions→Modifications),  localize e selecione a modificação com o nome 'OCMOD Editor', depois clique no botão Excluir (Delete), e no botão Atualizar (Refresh).

### Atualização

Acesse a administração da loja e execute o procedimento de Desinstalação, depois execute o procedimento de Instalação.

### Dúvidas

O OCMOD (OpenCart Modification) é nativo do OpenCart, ou seja, não é necessário instalar nenhum complemento no OpenCart para utilizar modificações ou extensões no formato OCMOD, para mais informações sobre o OCMOD, segue o link:

https://github.com/opencart/opencart/wiki/Modification-System

### Os arquivos alterados virtualmente através do OCMOD são:

admin/controller/extension/modification.php

admin/view/template/extension/modification.tpl

[versao-badge]: https://img.shields.io/badge/versão-2.0.0-blue.svg
[CHANGELOG]: ./CHANGELOG.md
[licenca-badge]: https://img.shields.io/badge/licença-GPLv3-blue.svg
[LICENSE]: ./LICENSE
