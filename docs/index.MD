# AdCommunityEdition
- [Grupo facebook](https://www.facebook.com/groups/adframework)
- [Grupo Google!](https://plus.google.com/communities/10142970538467959139)

Hlibs para Adianti - Rodrigo Moglia (moglia@interatia.com) - Fernando Pasqueto - Alexandre InDevWeb
Cansados de experar por atualizações estamos fazendo algumas atualizações para comportar necessidades
que surgiram ao longo do desenvolvimento de alguns projetos.

* Hlibs para Adianti - Atenção ESTAS Biliotecas são BETA/ALFA use por sua conta e risco!
* @copyright  Copyright (c) 2018 Interatia Sistemas de Informação. (http://www.interatia.com)
* @license    http://www.adianti.com.br/framework-license

Para que a classe funcione deve-se jogar o diretório em app\lib\hlib.
Caso queira usar os métodos Back de HCoreApplication e da HWindow

É necessário modificar o index.php e engine.php na raíz do framework.

index.php  //Adcionar esta linha no código próximo a linha 42

~~~ php

if ($class)
{
    $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : NULL;
    HCoreApplication::setHistory($_REQUEST);
    AdiantiCoreApplication::loadPage($class, $method, $_REQUEST);
}

engine.php //Adcionar esta linha no código próximo a linha 34

if( isset($programs[$class]) OR $public )
{
    HCoreApplication::setHistory($_REQUEST); 
    parent::run($debug);
}

~~~

# HWindow

~~~ php
//Determina uma ação a ser executada ao clicar no botão fechar
parent::closeAction($class, $method = NULL, $parameters = NULL, $callback = NULL, $type='load')

//Volta até 4 níveis apartir do ultimo controler aberto
closeBack($level=1, $type='load', $extra_param=array())

~~~ 

# HCoreApplication

~~~ php



//Retorna um objeto tipo TActionLink com o nível a retornar desejado
HCoreApplication::linkBack($value, $level=1, $extra_param=array(), $color = null, $size = null, $decoration = null, $icon = null)

//Retorna um objeto tipo TAction com o nível a retornar desejado pode ser usado nos botões como action normal
HCoreApplication::actionBack($level=1,$extra_param=array())

//Redireciona para uma determinado controller presente no histórico de acordo com o elemento de nível informado até 4 níveis
HCoreApplication::gotoBack($level=1,$extra_param=array()) 

//Carrega para uma determinado controller presente no histórico de acordo com o elemento de nível informado até 4 níveis
HCoreApplication::loadBack($level=1,$extra_param=array()) 

~~~ 



# HRepository - funções de agregação adcionais para TRepository

~~~ php

TTransaction::open(conexao.ini);
$repository = new HRepository('Model');
$retorno = $repository->count('criteria','coluna'); //por questões de compatibilidade
print_r($retorno);
echo "<br/>";
$retorno = $repository->sum('coluna');
print_r($retorno);
echo "<br/>";
$retorno = $repository->max('coluna');
print_r($retorno);
echo "<br/>";
$retorno = $repository->min('coluna');
print_r($retorno);
echo "<br/>";
$retorno = $repository->avg('coluna');
print_r($retorno);
echo "<br/>";

//Conta registros para usar na paginação faz a contagem ao banco uma única vez. Diminuindo load de banco de dados.
$count $repository->countOnce(TCriteria $criteria = NULL,$resetProperties=TRUE,$expression='');


TTransaction::close();

~~~


# HPanelGroup

~~~ php
HPanelGroup::($title = NULL, $background = NULL, $color = NULL) //parametros de cor de fundo e fonte

~~~

## Funções de formatação comuns (menos código no grid)

~~~ php
$coluna->setTransformer([HDateFormat::class, 'date2br']); ou 
HDateFormat::date2us($valor) // formata data novamente para ser armazenada
 
$coluna->setTransformer([HDateTimeFormat::class, 'datetime2br']); ou 
HDateTimeFormat::datetime2us($valor) // formata data novamente para ser armazenada

$coluna->setTransformer([HMoneyFormat::class, 'reais']); oi
HMoneyFormat::numeric($valor) // formata o número novamente para ser armazenado

~~~


# HDebug Depuração Simples

~~~ php
HDebug::debug($var,'titulo'); //retorna o valor como dialog
HDebug::raw($var,'titulo'); //retorna o valor bruto
HDebug::box($var,'titulo'); //retorna o valor dentro de uma textarea

~~~


# HPageNavigation

Exatamente igual a TPageNavigation em todos os aspectos entretanto ao instanciar um objeto da classe passando um nome no construtor permitirá colocar mais de uma paginação / grid na tela
mantendo o estado dos outros objetos via get e a paginação intacta nos outros paginadores independente.

~~~ php
$this->navegacao_nome = new HPageNavigation('nome');

//Use o método prepareParameters para receber somente os parametros de seu grid específico
$nome_param = $this->navegacao_nome->prepareParameters($nome_param); 

$criteria->setProperties($nome_param); //No criteria de seu loop de objetos que alimenta o seu grid.

$this->navegacao_nome->setProperties($nome_param); // E os parametros referentes a paginação 

//HClipboardButton - cria botão para copiar texto para área de transferencia

$botao = new HClipboardButton('botao');
$botao->setData('Texto a ser copiado para o clipboard');
$botao->setLabel('Copiar');
$botao->setImage('fa:copy green');

~~~ 
