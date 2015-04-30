### Resumo

Esta modificação foi desenvolvida no formato OCMod, e incrementa a possibilidade de editar e fazer download dos arquivos XML no formato OCMod através da administração do próprio OpenCart.

Uma das grandes vantagens desta modificação é permitir que, a edição dos XML no formato OCMod sejam feita através da administração da loja em um editor online, permitindo assim que os testes com as modificações sejam mais produtivos. Após as modificações é possível fazer o download do arquivo XML para OCMod com um clique.

Caso deseje doar um valor para contribuir com este trabalho continuo e sempre gratuito, clique no botão abaixo:

[![alt tag](https://www.paypalobjects.com/pt_BR/BR/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7G9TR9PXS6G5J)

### Instalação

 1. Baixe a modificação no link: https://github.com/opencartbrasil/ocmod-editor/releases
 2. Na administração da loja acesse o menu Extensions->Extension Installer (Extensões->Instalador).
 3. Na página do instalador, clique no botão Upload e selecione o arquivo 'ocmod-editor.ocmod.zip' (que você baixou deste repositório), e aguarde a conclusão da instalação automática.
 5. Após a instalação, acesse o menu Extensions->Modifications (Extensões->Modificações) e clique no botão Refresh (Atualizar), para que a modificação instalada seja incrementada na loja, lembrando que não é o botão "Atualizar" do navegador, e sim o botão "Atualizar" na cor azul ao lado do botão laranja e vermelho na tela do próprio OpenCart.

### Desinstalar

Para desinstalar a modificação, na administração da loja, acesse o menu Extensions->Modifications (Extensões->Modificações) e selecione a modificação com o nome 'OCMod Editor', depois clique no botão Delete (Excluir), e no botão Refresh (Atualizar).

### Utilização

Tanto a edição quanto o download do arquivo XML para OCMod são feitos através do menu Extensions->Modifications (Extensões->Modificações), onde devem aparecer após a correta instalação da modificação, os botões "Editar" e "Download".

### Dúvidas

O OCMod (OpenCart Modification) é nativo do OpenCart, ou seja, não é necessário instalar nenhum complemento no OpenCart para utilizar modificações ou extensões no formato OCMod, para mais informações sobre o OCMod, segue o link:

https://github.com/opencart/opencart/wiki/Modification-System

Os arquivos alterados virtualmente através do OCMod são:

admin/controller/extension/modification.php
admin/view/template/extension/modification.tpl

### Como contribuir

 1. Faça um Fork do projeto e edite os arquivos que desejar.
 2. Faça um Pull para que suas sugestões de melhorias sejam avaliadas e aceitas, caso aprovadas.
 3. Abra uma Inssue com sua dúvida ou sugestão.

### Licença

[GNU General Public License version 3 (GPLv3)](https://github.com/opencartbrasil/ocmod-editor/blob/master/LICENSE)
