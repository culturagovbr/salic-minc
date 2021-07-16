CONFIGURAÇÃO DO PDF

WINDOWS
1 - Criar a pasta "tmpPDF" dentro de "/public" e dar permissão de escrita na pasta
2 - Instalar OpenOffice
3 - Criar macro definida no fim deste arquivo

LINUX
1 - Criar a pasta "tmpPDF" dentro de "/public" e dar permissão de escrita na pasta
2 - Instalar OpenOffice
3 - Criar macro definida no fim deste arquivo
4 - Criar usu&aacute;rio "httpd" com senha 123456 por exemplo
5 - Abrir o arquivo "/etc/apache2/envvars" e substituir a linha "export APACHE_RUN_USER=www-data" por "export APACHE_RUN_USER=httpd" e então salvar o arquivo.
6 - Reiniciar o servidor Apache

MACRO

Abra o OpenOffice, v&aacute; em ferramentas, selecione o menu Macros->Organizar Macros->OpenOffice Basic. Na janela que abrir, navegue, na &aacute;rea "Macro de", para "Minhas Macros" -> "Standard" -> "Module1". Clique em "Editar" para editar o m&oacute;dulo "main" e então inclua o c&oacute;digo seguinte:

	REM  *****  BASIC  *****

	Sub ConvertWordToPDF(cFile)
	   cURL = ConvertToURL(cFile)

	   ' Open the document.
	   ' Just blindly assume that the document is of a type that OOo will
	   '  correctly recognize and open -- without specifying an import filter.
	   oDoc = StarDesktop.loadComponentFromURL(cURL, "_blank", 0, Array(MakePropertyValue("Hidden", True), ))

	   Dim comps
	   comps = split (cFile, ".")
	   If UBound(comps) > 0 Then
	       comps(UBound(comps)) = "pdf"
	       cfile = join (comps, ".")
	   Else
	       cfile = cFile + ".pdf"
	   Endif

	   cURL = ConvertToURL(cFile)

	   ' Save the document using a filter.
	   oDoc.storeToURL(cURL, Array(MakePropertyValue("FilterName", "writer_pdf_Export"), ))

	   oDoc.close(True)

	End Sub

	Function MakePropertyValue( Optional cName As String, Optional uValue ) As com.sun.star.beans.PropertyValue
	   Dim oPropertyValue As New com.sun.star.beans.PropertyValue
	   If Not IsMissing( cName ) Then
	      oPropertyValue.Name = cName
	   EndIf
	   If Not IsMissing( uValue ) Then
	      oPropertyValue.Value = uValue
	   EndIf
	   MakePropertyValue() = oPropertyValue
	End Function


Salve e pode sair do OpenOffice
