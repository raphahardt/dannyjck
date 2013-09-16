{block 'page.title' prepend}Teste - {/block}
{block 'page.contents'}
  <style>
    .ng-cloak {
      display:none !important;
    }
    .btn {
      margin-right: 3px;
    }
    .animate-if.ng-enter, .animate-if.ng-leave {
      -webkit-transition:all ease 0.3s;
      -moz-transition:all ease 0.3s;
      -o-transition:all ease 0.3s;
      transition:all ease 0.3s;
    }

    .animate-if.ng-enter,
    .animate-if.ng-leave.ng-leave-active {
      opacity:0;
      /*margin-top: -100px;*/
      -webkit-transform: scale(0);
    }

    .animate-if.ng-enter.ng-enter-active,
    .animate-if.ng-leave {
      opacity:1;
      margin-top:0;
      -webkit-transform: scale(1);
    }
    .translucid {
      opacity:0.2;
    }
    
    .fileinput-button {
      position: relative;
      overflow: hidden;
    }
    .fileinput-button input {
      position: absolute;
      top: 0;
      right: 0;
      margin: 0;
      opacity: 0;
      filter: alpha(opacity=0);
      transform: translate(-300px, 0) scale(4);
      font-size: 23px;
      direction: ltr;
      cursor: pointer;
    }
    
    ul.files {
      list-style: none;
      margin:0;
      padding:0;
    }
    ul.files li .file-container {
      /*float:left;*/
      /*width: 180px;
      height: 250px;*/
      border:1px solid #41495b;
      margin: 3px -10px;
      border-radius: 4px;
      position: relative;
    }
    ul.files li.even .file-container {
      border-right-style: dashed;
      /*margin-right: -14px;*/
    }
    ul.files li.odd .file-container {
      /*background: #212631;*/
      border-left-style: dashed;
      /*margin-left: -14px;*/
    }
    ul.files li .file-preview {
      margin: 20px 5px;
    }
    ul.files li .file-preview img,
    ul.files li .file-preview canvas{
      display:block;
      margin: 0 auto;
      border-radius: 4px;
      box-shadow: 1px 1px 3px #111621;
    }
    ul.files li .file-info {
      background: #212631;
      margin: 3px;
      padding: 8px;
      border-radius: 4px;
      font-size: 12px;
    }
    ul.files li .file-name, 
    ul.files li .file-size, 
    ul.files li .file-progress {
      height: 18px;
      line-height: 18px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      float:left;
    }
    ul.files li .file-name,
    ul.files li .file-size {
      width: 24.91%;
    }
    ul.files li .file-progress {
      width: 50%;
    }
    ul.files li .file-actions {
      padding-top: 3px;
      clear: left;
      text-align: right;
    }
    ul.files li .file-number {
      position: absolute;
      top: 0;
      left: 0;
      width: 40px;
      height: 32px;
      background: #212631;
      color: #ffffff;
      text-align: center;
      line-height: 32px;
      font-size: 16px;
      border-radius: 4px;
    }
    ul.files li.odd .file-number {
      right: 0;
      left: auto;
    }
    /*ul.files li .file-number span:before {
      content: "< ";
    }
    ul.files li.odd .file-number span:before {
      content: "> ";
    }*/
  </style>
  
  <div class="form-group">
    <label>Teste</label>
    <input type="text" class="form-control"/>
  </div>

  <!-- The file upload form used as target for the file upload widget -->
  <form id="fileupload" action="{$site.URL}/public/upload/" method="POST" enctype="multipart/form-data" ng-app="demo" ng-controller="DemoFileUploadController" data-file-upload="options" ng-class="{ 'fileupload-processing': processing() || loadingFiles }">
    <!-- Redirect browsers with JavaScript disabled to the origin page -->
    <noscript><input type="hidden" name="redirect" value="{$site.URL}/"></noscript>
    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
    <div class="row fileupload-buttonbar">
      <div class="col-lg-7">
        <!-- The fileinput-button span is used to style the file input field as button -->
        <span class="btn btn-primary fileinput-button" ng-class="{ disabled: disabled }">
          <span>Escolher imagens...</span>
          <input type="file" name="files[]" multiple ng-disabled="disabled">
        </span>
        <button type="button" class="btn btn-default start" ng-click="submit()">
          <span>Iniciar upload</span>
        </button>
        <button type="button" class="btn btn-default cancel" ng-click="cancel()">
          <span>Cancelar upload</span>
        </button>
        <!-- The loading indicator is shown during file processing -->
        <div class="fileupload-loading"></div>
      </div>
      <!-- The global progress information -->
      <div class="col-lg-5 fade" ng-class="{ in: active()}">
        <!-- The global progress bar -->
        <div class="progress progress-striped active" data-file-upload-progress="progress()"><div class="progress-bar progress-bar-success" ng-style="{ width: num + '%'}"></div></div>
        <!-- The extended global progress information -->
        <div class="progress-extended">&nbsp;</div>
      </div>
    </div>
    
    <!-- The table listing the files available for upload/download -->
    <ul class="files ng-cloak" ui-sortable="sortableOptions" ng-model="queue">
      <li ng-repeat="file in queue" class="col-lg-3 col-md-4 col-sm-6" ng-class-odd="'odd'" ng-class-even="'even'">
        <div class="file-container">
          <div class="file-preview" ng-switch on="!!file.thumbnailUrl">
            <div class="preview" ng-switch-when="true">
              <a ng-href="[{ file.url }]" title="[{ file.name }]" download="[{ file.name }]" data-gallery><img ng-src="[{ file.thumbnailUrl }]" alt=""></a>
            </div>
            <div class="preview translucid" ng-switch-default data-file-upload-preview="file"></div>
          </div>

          <div class="file-info">
            <div class="file-name">
              <p class="name" ng-switch on="!!file.url">
                <span ng-switch-when="true" ng-switch on="!!file.thumbnailUrl">
                  <a ng-switch-when="true" ng-href="[{ file.url }]" title="[{ file.name }]" download="[{ file.name }]" data-gallery>[{ file.name }]</a>
                  <a ng-switch-default ng-href="[{ file.url }]" title="[{ file.name }]" download="[{ file.name }]">[{ file.name }]</a>
                </span>
                <span ng-switch-default>[{ file.name }]</span>
              </p>
              <div ng-show="file.error"><span class="label label-danger">Error</span> [{ file.error }]</div>
            </div>
            <div class="file-size">
              <p class="size">[{ file.size | formatFileSize }]</p>
            </div>
            <div class="file-progress">
              <div class="progress progress-striped active fade" ng-class="{ pending: 'in' }[file.$state()]" data-file-upload-progress="file.$progress()"><div class="progress-bar progress-bar-success" ng-style="{ width: num + '%'}"></div></div>
            </div>

            <div class="file-actions">
              <button type="button" class="btn btn-primary btn-sm start" ng-click="file.$submit()" ng-hide="!file.$submit">
                <span>Upload</span>
              </button>
              <button type="button" class="btn btn-default btn-sm cancel" ng-click="file.$cancel()" ng-hide="!file.$cancel">
                <span>Cancelar</span>
              </button>
              <button ng-controller="FileDestroyController" type="button" class="btn btn-default btn-sm destroy" ng-click="file.$destroy()" ng-hide="!file.$destroy">
                <span>Excluir</span>
              </button>
            </div>
          </div>

          <div class="file-number" ng-if="!$odd"><span title="Página da direita">[{ $index+1 }]</span></div>
          <div class="file-number" ng-if="!$even"><span title="Página da esquerda">[{ $index+1 }]</span></div>
          
        </div>
      </li>
    </ul>
  </form>
  <div style="clear:left;">&nbsp;</div>
  
{/block}
{block 'page.js' append}
  <!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
  <script src="{$site.URL}/../public/js/helpers/jupload/vendor/jquery.ui.widget.js"></script>
  <!-- The Load Image plugin is included for the preview images and image resizing functionality -->
  <script src="{$site.URL}/../public/js/helpers/jupload/plugins/load-image.min.js"></script>
  <!-- The Canvas to Blob plugin is included for image resizing functionality -->
  <script src="{$site.URL}/../public/js/helpers/jupload/plugins/canvas-to-blob.min.js"></script>
  
  <script src="{$site.URL}/../public/js/helpers/jupload/jquery.iframe-transport.js"></script>
  <!-- The basic File Upload plugin -->
  <script src="{$site.URL}/../public/js/helpers/jupload/jquery.fileupload.js"></script>
  <!-- The File Upload processing plugin -->
  <script src="{$site.URL}/../public/js/helpers/jupload/jquery.fileupload-process.js"></script>
  <!-- The File Upload image preview & resize plugin -->
  <script src="{$site.URL}/../public/js/helpers/jupload/jquery.fileupload-image.js"></script>
  <!-- The File Upload validation plugin -->
  <script src="{$site.URL}/../public/js/helpers/jupload/jquery.fileupload-validate.js"></script>
  <!-- The File Upload Angular JS module -->
  <script src="{$site.URL}/../public/js/helpers/jupload/jquery.fileupload-angular.js"></script>
  <!-- The main application script -->
  <script>
  
(function () {
    'use strict';

    var url = '{$site.URL}/public/upload/';

{literal}
    angular.module('demo', [
        'blueimp.fileupload',
        'ngAnimate'
    ])
        .config([
            '$httpProvider', '$interpolateProvider', 'fileUploadProvider',
            function ($httpProvider, $interpolateProvider, fileUploadProvider) {
                $interpolateProvider.startSymbol('[{').endSymbol('}]');
                
                delete $httpProvider.defaults.headers.common['X-Requested-With'];
                fileUploadProvider.defaults.redirect = window.location.href.replace(
                    /\/[^\/]*$/,
                    '/cors/result.html?%s'
                    );
                  // settings
                  angular.extend(fileUploadProvider.defaults, {
                      previewMaxWidth: 250,
                      previewMaxHeight: 350,
                      limitConcurrentUploads: 3,
                      // Enable image resizing, except for Android and Opera,
                      // which actually support image resizing, but fail to
                      // send Blob objects via XHR requests:
                      disableImageResize: /Android(?!.*Chrome)|Opera/
                              .test(window.navigator.userAgent),
                      maxFileSize: 5000000,
                      acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i
                    });
                }
                ])

                .controller('DemoFileUploadController', [
                  '$scope', '$http', '$filter', '$window',
                  function($scope, $http) {
                    $scope.options = {
                      url: url
                    };
                    $scope.sortableOptions = {
                      //update: function(e, ui) { ... },
                      axis: 'x'
                    };
                    $scope.loadingFiles = true;
                    $http.get(url)
                      .then(
                      function(response) {
                        $scope.loadingFiles = false;
                        $scope.queue = response.data.files || [];
                      },
                      function() {
                        $scope.loadingFiles = false;
                      }
                    );
                  }
                ])

                .controller('FileDestroyController', [
                  '$scope', '$http',
                  function($scope, $http) {
                    var file = $scope.file,
                            state;
                    if (file.url) {
                      file.$state = function() {
                        return state;
                      };
                      file.$destroy = function() {
                        state = 'pending';
                        return $http({
                          url: file.deleteUrl,
                          method: file.deleteType
                        }).then(
                          function() {
                            state = 'resolved';
                            $scope.clear(file);
                          },
                          function() {
                            state = 'rejected';
                          }
                        );
                      };
                    } else if (!file.$cancel && !file._index) {
                      file.$cancel = function() {
                        $scope.clear(file);
                      };
                    }
                  }
                ]);

              }());

  {/literal}
  </script>
{/block}