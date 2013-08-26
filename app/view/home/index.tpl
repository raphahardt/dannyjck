{block 'page.title' prepend}{$view.title}aaaaaaaaaaaaaaaaaaaaaaa - {/block}
{block 'page.contents'}
  {literal}
  <div ng-controller="HomeCtrl">
    <p>{{itens.length}} itens encontrados</p>
    <ul>
      <li ng-repeat="item in itens">
        <img ng-src="{{item.img}}" width="50" />
        <input type="checkbox" ng-model="item.checked" />
        <span>{{item.nome}}</span>
      </li>
    </ul>
    <a href="#" ng-click="addA()">add</a>
  </div>
  {/literal}
{/block}
{block 'ad.728'}{/block}
{block 'page.js' append}
  <script>
    function HomeCtrl($scope) {
      $scope.itens = [
        { img: '123.jpg', nome: 'Teste1', checked:true },
        { img: '123.jpg', nome: 'Teste2', checked:true },
        { img: '123.jpg', nome: 'Teste3', checked:false },
        { img: '123.jpg', nome: 'Teste4', checked:true },
        { img: '123.jpg', nome: 'Teste5', checked:false }
      ];
      
      $scope.addA = function () {
        $scope.itens.push({ img: '123.jpg', nome: 'Teste1', checked:true });
      }
    }
  </script>
{/block}