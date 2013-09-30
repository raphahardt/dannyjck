<?php

/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class UserMapper extends DbcMapper {
  
  function __construct() {
    
    $table = new SQLTable('fm_servicos', 's');
    $table->addField('id');
    $table->addField('descricao');
    $table->addField('ativo');
    
    $this->setEntity($table);
    
    parent::__construct();
  }
  
  
}

// inicialização da tabela teste
$db = Dbc::getInstance();
// limpa
$db->prepare("TRUNCATE fm_servicos");
$db->execute();
// popula
$db->prepare("
INSERT INTO `fm_servicos` (`id`, `descricao`, `ativo`) VALUES
(1, 'Suspensão', 1),
(2, 'Alinhamento', 1),
(3, 'Balanceamento', 1),
(4, 'Embreagem', 1),
(5, 'Câmbio', 1),
(6, 'Freios', 1),
(7, 'Pneus', 1),
(8, 'Vidros', 1),
(9, 'Auto-Elétrica', 1),
(10, 'Injeção Eletrônica', 1),
(11, 'Som', 1),
(12, 'Funilaria', 1),
(13, 'Pintura', 1),
(14, 'Polimento', 1);
");
$db->execute();
$db->free();
unset($db);

$user = new UserMapper();
$user->setFilter(_c($user->getEntity()->descricao, '!=', null));

echo it('instancia do usermapper criada')->expect($user)->toInstanceOf('UserMapper');

echo it('procura todos os registros não nulos da tabela')->expect($user->select())->toBe(true);
echo it('contagem dos registros da tabela')->expect($user->count())->toBe(14);
echo it('valor descricao do primeiro registro (selecionado)')->expect($user['descricao'])->toBe('Suspensão');
echo it('valor id do primeiro registro (selecionado)')->expect($user['id'])->toBe(1, false);

$user->next();

echo it('valor descricao do segundo registro (selecionado)')->expect($user['descricao'])->toBe('Alinhamento');

$user->last();

echo it('valor descricao do ultimo registro (selecionado)')->expect($user['descricao'])->toBe('Polimento');

$user->find(8);

echo it('valor descricao do registro id 8 (selecionado)')->expect($user['descricao'])->toBe('Vidros');

$user->setFilter(_c($user->getEntity()->descricao, '=', 'Vidros'));
$user->delete();
$user->select();

echo it('select com o mesmo fitlro do delete')->expect($user->count())->toBe(0);

$user->setFilter(_c($user->getEntity()->descricao, '!=', null));
echo it('procura todos os registros não nulos da tabela')->expect($user->select())->toBe(true);
echo it('select com o filtro antigo (descricao != null)')->expect($user->count())->toBe(13);

exit;
// ======================================================================================

$array = new FileMapper();

echo it('numero de elementos')->expect($array->count())->toBe(0);

$array->push();

echo it('numero de elementos depois do push vazio')->expect($array->count())->toBe(0);

$array['id'] = 1;
$array['campo1'] = 10;
$array->push();

echo it('numero de elementos depois do push com registro')->expect($array->count())->toBe(1);
echo it('valor do campo1 depois do push com registro')->expect($array['campo1'])->toBe(10);
echo it('nome do campo1 usando letras maiusculas (CaMpO1)')->expect($array->CaMpO1)->toBe('campo1');
echo it('valor do campo2 (nao existe)')->expect($array['campo2'])->toBe(null);

$array->unshift();

echo it('numero de elementos depois do unshift com registro anterior')->expect($array->count())->toBe(2);
echo it('valor do campo1 depois do unshift com registro')->expect($array['campo1'])->toBe(10);

$array['campo1'] += 20;

echo it('valor do campo1 somado ele mesmo += 20')->expect($array['campo1'])->toBe(30);

$array['campo1']++;

echo it('valor do campo1 incrementado ++')->expect($array['campo1'])->notToBe(31);

$array->find(1);

echo it('valor do campo1 ser o primeiro campo id = 1')->expect($array['campo1'])->notToBe(30);
echo it('valor do campo1 ser o primeiro campo id = 1')->expect($array['campo1'])->toBe(10);

$result = $array->remove();

echo it('valor do resultado após deletar')->expect($result)->toBe(true);
echo it('valor do campo1 após deletar')->expect($array['campo1'])->toBe(null);

echo it('deletar vazio')->expect($array->remove())->toBe(false);
echo it('deletar não existente')->expect($array->remove(999))->toBe(false);
echo it('deletar outro id 1')->expect($array->remove(1))->toBe(true);

echo it('numero de elementos depois de deletar os dois id 1')->expect($array->count())->toBe(0);

$i = 11;
for($j=0;$j<5;$j++)
$array->push(array(
    //'id' => $i++,
    'nome' => mt_rand()
));

echo it('apos inserir 5 registros')->expect($array->count())->toBe(5);
echo it('o ponteiro interno estar no ultimo registro (sem get)')->expect($array['id'])->toBe(null);
$array->get();
echo it('o ponteiro interno estar no ultimo registro (apos get)')->expect($array['id'])->toBe(5);

echo it('busca pelo id 2')->expect($array->find(2))->toBe(1);
echo it('busca pelo id 2 valor')->expect($array['id'])->toBe(2);

echo it('busca pelo id 4')->expect($array->find(4))->toBe(3);
echo it('busca pelo id 4 valor')->expect($array['id'])->toBe(4);

echo it('busca pelo id 9999')->expect($array->find(9999))->toBe(false);
echo it('busca pelo id 9999 valor')->expect($array['id'])->toBe(null);

$array->unshift(array(
    'id' => 90,
    'nome' => 'joao'
));
$array->setPointer('nome');

echo it('adicionar um registro com nome "joao" (90), definir o id como nome e buscar por "joao"')->expect($array->find('joao'))->notToBe(false);
