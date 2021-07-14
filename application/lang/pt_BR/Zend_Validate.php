<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage Ressource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Zend_Validate.php 1361 2011-06-07 17:40:12Z ruy.ferreira@medicando.com.br $
 */

/**
 * EN-Revision: 20377
 */
return array(
        // Traducao do Grid
        "Show" => "Mostrar",
        "items" => "Itens",
        "Select" => "Escolha",
        "Apply Filter" => "Aplicar Filtro",
        "Remove Order" => "Remover ordenação",
        "Remove Filters" => "Remover Filtros",
        "Remove Filters and Order" => "Remover Filtros e Ordenação",
        "Page" => "P&aacute;gina",
        "Next" => "Pr&oacute;xima",
        "Last" => "&uacute;ltima",
        "First" => "Primeira",
        "Previous" => "Anterior",
        "to" => "at&eacute;",
        "of" => "de",

    // Zend_Validate_Captcha
    "Captcha value is wrong" => "A verificação est&aacute; incorreta",
	"Empty captcha value" => "A verificação est&aacute; vazia",

    // Zend_Validate_Alnum
    "Invalid type given, value should be float, string, or integer" => "Tipo especificado inv&aacute;lido, o valor deve ser float, string, ou inteiro",
    "'%value%' contains characters which are non alphabetic and no digits" => "'%value%' cont&eacute;m caracteres que não são alfab&eacute;ticos e nem d&iacute;gitos",
    "'%value%' is an empty string" => "'%value%' &eacute; uma string vazia",

    // Zend_Validate_Alpha
    "Invalid type given, value should be a string" => "Tipo especificado inv&aacute;lido, o valor deve ser uma string",
    "'%value%' contains non alphabetic characters" => "'%value%' cont&eacute;m caracteres não alfab&eacute;ticos",

    // Zend_Validate_Barcode
    "'%value%' failed checksum validation" => "'%value%' falhou na validação do checksum",
    "'%value%' contains invalid characters" => "'%value%' cont&eacute;m caracteres inv&aacute;lidos",
    "'%value%' should have a length of %length% characters" => "'%value%' tem um comprimento de %length% caracteres",
    "Invalid type given, value should be string" => "Tipo especificado inv&aacute;lido, o valor deve ser string",

    // Zend_Validate_Between
    "'%value%' is not between '%min%' and '%max%', inclusively" => "'%value%' não est&aacute; entre '%min%' e '%max%', inclusivamente",
    "'%value%' is not strictly between '%min%' and '%max%'" => "'%value%' não est&aacute; exatamente entre '%min%' e '%max%'",

    // Zend_Validate_Callback
    "'%value%' is not valid" => "'%value%' não &eacute; v&aacute;lido",
    "Failure within the callback, exception returned" => "Falha na chamada de retorno, exceção retornada",

    // Zend_Validate_Ccnum
    "'%value%' must contain between 13 and 19 digits" => "'%value%' deve conter entre 13 e 19 d&iacute;gitos",
    "Luhn algorithm (mod-10 checksum) failed on '%value%'" => "O algoritmo de Luhn (checksum de m&oacute;dulo 10) falhou em '%value%'",

    // Zend_Validate_CreditCard
    "'%value%' must contain only digits" => "'%value%' deve conter apenas d&iacute;gitos",
    "'%value%' contains an invalid amount of digits" => "'%value%' cont&eacute;m uma quantidade inv&aacute;lida de d&iacute;gitos",
    "'%value%' is not from an allowed institute" => "'%value%' não vem de uma instituição autorizada",
    "Validation of '%value%' has been failed by the service" => "A validação de '%value%' falhou por causa do serviço",
    "The service returned a failure while validating '%value%'" => "O serviço devolveu um erro enquanto validava '%value%'",

    // Zend_Validate_Date
    "Invalid type given, value should be string, integer, array or Zend_Date" => "Tipo especificado inv&aacute;lido, o valor deve ser string, inteiro, matriz ou Zend_Date",
    "'%value%' does not appear to be a valid date" => "'%value%' não parece ser uma data v&aacute;lida",
    "'%value%' does not fit the date format '%format%'" => "'%value%' não se encaixa no formato de data '%format%'",

    // Zend_Validate_Db_Abstract
    "No record matching %value% was found" => "Não foram encontrados registros para %value%",
    "A record matching %value% was found" => "Um registro foi encontrado para %value%",

    // Zend_Validate_Digits
    "Invalid type given, value should be string, integer or float" => "Tipo especificado inv&aacute;lido, o valor deve ser string, inteiro ou float",
    "'%value%' contains not only digit characters" => "'%value%' não cont&eacute;m apenas n&uacute;meros",

    // Zend_Validate_EmailAddress
    "'%value%' is no valid email address in the basic format local-part@hostname" => "'%value%' não &eacute; um endereço de e-mail v&aacute;lido no formato local-part@hostname",
    "'%hostname%' is no valid hostname for email address '%value%'" => "'%hostname%' não &eacute; um nome de host v&aacute;lido para o endereço de e-mail '%value%'",
    "'%hostname%' does not appear to have a valid MX record for the email address '%value%'" => "'%hostname%' não parece ter um registro MX v&aacute;lido para o endereço de e-mail '%value%'",
    "'%hostname%' is not in a routable network segment. The email address '%value%' should not be resolved from public network." => "'%hostname%' não &eacute; um segmento de rede rote&aacute;vel. O endereço de e-mail '%value%' não deve ser resolvido a partir de um rede p&uacute;blica.",
    "'%localPart%' can not be matched against dot-atom format" => "'%localPart%' não corresponde com o formato dot-atom",
    "'%localPart%' can not be matched against quoted-string format" => "'%localPart%' não corresponde com o formato quoted-string",
    "'%localPart%' is no valid local part for email address '%value%'" => "'%localPart%' não &eacute; uma parte local v&aacute;lida para o endereço de e-mail '%value%'",
    "'%value%' exceeds the allowed length" => "'%value%' excede o comprimento permitido",

    // Zend_Validate_File_Count
    "Too many files, maximum '%max%' are allowed but '%count%' are given" => "H&aacute; muitos arquivos, são permitidos no m&aacute;ximo '%max%', mas '%count%' foram fornecidos",
    "Too few files, minimum '%min%' are expected but '%count%' are given" => "H&aacute; poucos arquivos, são esperados no m&iacute;nimo '%min%', mas '%count%' foram fornecidos",

    // Zend_Validate_File_Crc32
    "File '%value%' does not match the given crc32 hashes" => "O arquivo '%value%' não corresponde ao hash crc32 fornecido",
    "A crc32 hash could not be evaluated for the given file" => "Não foi poss&iacute;vel avaliar um hash crc32 para o arquivo fornecido",
    "File '%value%' could not be found" => "O arquivo '%value%' não pôde ser encontrado",

    // Zend_Validate_File_ExcludeExtension
    "File '%value%' has a false extension" => "O arquivo '%value%' possui a extensão incorreta",

    // Zend_Validate_File_ExcludeMimeType
    "File '%value%' has a false mimetype of '%type%'" => "O arquivo '%value%' tem o mimetype incorreto: '%type%'",
    "The mimetype of file '%value%' could not be detected" => "O mimetype do arquivo '%value%' não pôde ser detectado",
    "File '%value%' can not be read" => "O arquivo '%value%' não pôde ser lido",

    // Zend_Validate_File_Exists
    "File '%value%' does not exist" => "O arquivo '%value%' não existe",

    // Zend_Validate_File_Extension

    // Zend_Validate_File_FilesSize
    "All files in sum should have a maximum size of '%max%' but '%size%' were detected" => "Todos os arquivos devem ter um tamanho m&aacute;ximo de '%max%', mas um tamanho de '%size%' foi detectado",
    "All files in sum should have a minimum size of '%min%' but '%size%' were detected" => "Todos os arquivos devem ter um tamanho m&iacute;nimo de '%min%', mas um tamanho de '%size%' foi detectado",
    "One or more files can not be read" => "Um ou mais arquivos não puderam ser lidos",

    // Zend_Validate_File_Hash
    "File '%value%' does not match the given hashes" => "O arquivo '%value%' não corresponde ao hash fornecido",
    "A hash could not be evaluated for the given file" => "Não foi poss&iacute;vel avaliar um hash para o arquivo fornecido",

    // Zend_Validate_File_ImageSize
    "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected" => "A largura m&aacute;xima permitida para a imagem '%value%' deve ser '%maxwidth%', mas '%width%' foi detectada",
    "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected" => "A largura m&iacute;nima esperada para a imagem '%value%' deve ser '%minwidth%', mas '%width%' foi detectada",
    "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected" => "A altura m&aacute;xima permitida para a imagem '%value%' deve ser '%maxheight%', mas '%height%' foi detectada",
    "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected" => "A altura m&iacute;nima esperada para a imagem '%value%' deve ser '%minheight%', mas '%height%' foi detectada",
    "The size of image '%value%' could not be detected" => "O tamanho da imagem '%value%' não pôde ser detectado",

    // Zend_Validate_File_IsCompressed
    "File '%value%' is not compressed, '%type%' detected" => "O arquivo '%value%' não est&aacute; compactado: '%type%' detectado",

    // Zend_Validate_File_IsImage
    "File '%value%' is no image, '%type%' detected" => "O arquivo '%value%' não &eacute; uma imagem: '%type%' detectado",

    // Zend_Validate_File_Md5
    "File '%value%' does not match the given md5 hashes" => "O arquivo '%value%' não corresponde ao hash md5 fornecido",
    "A md5 hash could not be evaluated for the given file" => "Não foi poss&iacute;vel avaliar um hash md5 para o arquivo fornecido",

    // Zend_Validate_File_MimeType

    // Zend_Validate_File_NotExists
    "File '%value%' exists" => "O arquivo '%value%' existe",

    // Zend_Validate_File_Sha1
    "File '%value%' does not match the given sha1 hashes" => "O arquivo '%value%' não corresponde ao hash sha1 fornecido",
    "A sha1 hash could not be evaluated for the given file" => "Não foi poss&iacute;vel avaliar um hash sha1 para o arquivo fornecido",

    // Zend_Validate_File_Size
    "Maximum allowed size for file '%value%' is '%max%' but '%size%' detected" => "O tamanho m&aacute;ximo permitido para o arquivo '%value%' &eacute; '%max%', mas '%size%' foram detectados",
    "Minimum expected size for file '%value%' is '%min%' but '%size%' detected" => "O tamanho m&iacute;nimo esperado para o arquivo '%value%' &eacute; '%min%', mas '%size%' foram detectados",

    // Zend_Validate_File_Upload
    "File '%value%' exceeds the defined ini size" => "O arquivo '%value%' excede o tamanho definido na configuração",
    "File '%value%' exceeds the defined form size" => "O arquivo '%value%' excede o tamanho definido do formul&aacute;rio",
    "File '%value%' was only partially uploaded" => "O arquivo '%value%' foi apenas parcialmente enviado",
    "File '%value%' was not uploaded" => "O arquivo '%value%' não foi enviado",
    "No temporary directory was found for file '%value%'" => "Nenhum diret&oacute;rio tempor&aacute;rio foi encontrado para o arquivo '%value%'",
    "File '%value%' can't be written" => "O arquivo '%value%' não pôde ser escrito",
    "A PHP extension returned an error while uploading the file '%value%'" => "Uma extensão do PHP retornou um erro enquanto o arquivo '%value%' era enviado",
    "File '%value%' was illegally uploaded. This could be a possible attack" => "O arquivo '%value%' foi enviado ilegalmente. Este poderia ser um poss&iacute;vel ataque",
    "File '%value%' was not found" => "O arquivo '%value%' não foi encontrado",
    "Unknown error while uploading file '%value%'" => "Erro desconhecido ao enviar o arquivo '%value%'",

    // Zend_Validate_File_WordCount
    "Too much words, maximum '%max%' are allowed but '%count%' were counted" => "H&aacute; muitas palavras, são permitidas no m&aacute;ximo '%max%', mas '%count%' foram contadas",
    "Too less words, minimum '%min%' are expected but '%count%' were counted" => "H&aacute; poucas palavras, são esperadas no m&iacute;nimo '%min%', mas '%count%' foram contadas",

    // Zend_Validate_Float
    "'%value%' does not appear to be a float" => "'%value%' não parece ser um float",

    // Zend_Validate_GreaterThan
    "'%value%' is not greater than '%min%'" => "'%value%' não &eacute; maior que '%min%'",

    // Zend_Validate_Hex
    "'%value%' has not only hexadecimal digit characters" => "'%value%' não cont&eacute;m somente caracteres hexadecimais",

    // Zend_Validate_Hostname
    "'%value%' appears to be an IP address, but IP addresses are not allowed" => "'%value%' parece ser um endereço de IP, mas endereços de IP não são permitidos",
    "'%value%' appears to be a DNS hostname but cannot match TLD against known list" => "'%value%' parece ser um hostname de DNS, mas o TLD não corresponde a nenhum TLD conhecido",
    "'%value%' appears to be a DNS hostname but contains a dash in an invalid position" => "'%value%' parece ser um hostname de DNS, mas cont&eacute;m um traço em uma posição inv&aacute;lida",
    "'%value%' appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'" => "'%value%' parece ser um hostname de DNS, mas não corresponde ao esquema de hostname para o TLD '%tld%'",
    "'%value%' appears to be a DNS hostname but cannot extract TLD part" => "'%value%' parece ser um hostname de DNS, mas o TLD não pôde ser extra&iacute;do",
    "'%value%' does not match the expected structure for a DNS hostname" => "'%value%' não corresponde com a estrutura esperada para um hostname de DNS",
    "'%value%' does not appear to be a valid local network name" => "'%value%' não parece ser um nome de rede local v&aacute;lido",
    "'%value%' appears to be a local network name but local network names are not allowed" => "'%value%' parece ser um nome de rede local, mas os nomes de rede local não são permitidos",
    "'%value%' appears to be a DNS hostname but the given punycode notation cannot be decoded" => "'%value%' parece ser um hostname de DNS, mas a notação punycode fornecida não pode ser decodificada",

    // Zend_Validate_Iban
    "Unknown country within the IBAN '%value%'" => "Pa&iacute;s desconhecido para o IBAN '%value%'",
    "'%value%' has a false IBAN format" => "'%value%' não &eacute; um formato IBAN v&aacute;lido",
    "'%value%' has failed the IBAN check" => "'%value%' falhou na verificação do IBAN",

    // Zend_Validate_Identical
    "The token '%token%' does not match the given token '%value%'" => "A marca '%token%' não corresponde a marca '%value%' fornecida",
    "No token was provided to match against" => "Nenhuma marca foi fornecida para a comparação",

    // Zend_Validate_InArray
    "'%value%' was not found in the haystack" => "'%value%' não faz parte dos valores esperados",

    // Zend_Validate_Int
    "Invalid type given, value should be string or integer" => "Tipo especificado inv&aacute;lido, o valor deve ser string ou inteiro",
    "'%value%' does not appear to be an integer" => "'%value%' não parece ser um n&uacute;mero inteiro",

    // Zend_Validate_Ip
    "'%value%' does not appear to be a valid IP address" => "'%value%' não parece ser um endereço de IP v&aacute;lido",

    // Zend_Validate_Isbn
    "'%value%' is no valid ISBN number" => "'%value%' não &eacute; um n&uacute;mero ISBN v&aacute;lido",

    // Zend_Validate_LessThan
    "'%value%' is not less than '%max%'" => "'%value%' não &eacute; menor que '%max%'",

    // Zend_Validate_NotEmpty
    "Invalid type given, value should be float, string, array, boolean or integer" => "Tipo especificado inv&aacute;lido, o valor deve ser float, string, matriz, booleano ou inteiro",
    "Value is required and can't be empty" => "O campo &eacute; obrigat&oacute;rio e não pode estar vazio",

    // Zend_Validate_PostCode
    "'%value%' does not appear to be an postal code" => "'%value%' não parece ser um c&oacute;digo postal",

    // Zend_Validate_Regex
    "'%value%' does not match against pattern '%pattern%'" => "'%value%' não corresponde ao padrão '%pattern%'",

    // Zend_Validate_Sitemap_Changefreq
    "'%value%' is no valid sitemap changefreq" => "'%value%' não &eacute; um changefreq de sitemap v&aacute;lido",

    // Zend_Validate_Sitemap_Lastmod
    "'%value%' is no valid sitemap lastmod" => "'%value%' não &eacute; um lastmod de sitemap v&aacute;lido",

    // Zend_Validate_Sitemap_Loc
    "'%value%' is no valid sitemap location" => "'%value%' não &eacute; uma localização de sitemap v&aacute;lida",

    // Zend_Validate_Sitemap_Priority
    "'%value%' is no valid sitemap priority" => "'%value%' não &eacute; uma prioridade de sitemap v&aacute;lida",

    // Zend_Validate_StringLength
    "'%value%' is less than %min% characters long" => "O tamanho de '%value%' &eacute; inferior a %min% caracteres",
    "'%value%' is more than %max% characters long" => "O tamanho de '%value%' &eacute; superior a %max% caracteres",
);
?>
