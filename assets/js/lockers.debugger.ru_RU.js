(function($){
    "use strict";
    
    if ( !window.bizpanda ) window.bizpanda = {};
    window.bizpanda.debugger = window.bizpanda.debugger || {
        
        init: function(){
            var self = this;
            
            $.pandalocker.hooks.add( 'opanda-cancel', function( identity, api, sender, sernderName, value ){
                self.highlightLockedContent( identity, api, sender, sernderName, value );
            }); 
        },
        
        highlightLockedContent: function( identity, api, sender ) {
            if ( !identity.content ) return;
            var self = this;
            
            identity.content.addClass('bp-content-to-debug');
            
            var $bracket = $("<div class='bp-debugger-bracket'></div>");
            $bracket.appendTo( identity.content );

            var $label = $("<div class='bp-debugger-label'><strong>Отладка:</strong> этот контент должен быть заблокирован, но он открыт. <a href='#' class='bp-why'>Почему?</a></div>");
            var $bracketTop = $("<div class='bp-debugger-bracket-top'></div>");
            var $bracketBottom = $("<div class='bp-debugger-bracket-bottom'></div>");
            
            $label.appendTo(identity.content);   
            $bracketTop.appendTo(identity.content);
            $bracketBottom.appendTo(identity.content);
            
            $label.find(".bp-why").click(function(){
                self.explain( identity, api, sender, 'unlocked' );
                return false;
            });
        },
        
        explain: function( identity, api, sender ) {
            if ( !identity.content ) return;
            
            if ( identity.content.hasClass('bp-explained') ) {
                identity.content.find('.bp-debugger-reason').hide();
                identity.content.removeClass('bp-explained');
            } else {
                identity.content.addClass('bp-explained');
                
                var $phare = $('<div></div>');
                var $details = null;
                //Содержание раскрывается благодаря тому, что пользователь разблокировать его стандартным способом. Очистить куки / локальное хранение вашего браузера, чтобы увидеть шкафчик снова, или открыть эту страницу в закладки частной.
                var phare = 'Ваш контент отктыт по причине ';

                if ( 'visibility' === sender ) {
                    phare = phare + ' <strong>условий видимости</strong>.';
                    $details = this.explainVisibilityConditions( identity, api );
                } else if ( 'provider' === sender ) { 
                    phare = phare + 'того, что его разблокировал пользователь <strong>стандартным путем</strong>, нажав на одну из социальных кнопок. Если замок был открыт, данные об этом сохраняются в локальном хранилище или cookie, вам нужно сбросить эти данные вручную или открыть страницу в режиме инкогнито.';
                    $details = this.explainStandartWay( identity, api );
                } else if ( 'ie7' === sender ) { 
                    phare = phare + 'того, что плагин не может корректно работать в браузере <strong>IE7</strong>.';
                } else if ( 'mobile' === sender ) { 
                    phare = phare + 'того, что замок <strong>скрыт для мобильных</strong> устройств.';
                } else { 
                    phare = phare + 'возникновения неизвестной ошибки.';
                }
                
                $phare.html(phare);

                var $reason = $("<div class='bp-debugger-reason'></div>");
                $reason.hide();

                $reason.append($phare);     
                if ( $details ) $reason.append($details);

                identity.content.prepend($reason);
                $reason.fadeIn(200);
            }
        },
        
        explainVisibilityConditions: function( identity, api ) {
            if ( !api || !api.options || !api.options.locker || !api.options.locker.visibility  ) return false;
            var visibility = api.options.locker.visibility;
            
            this.visibilityService = new $.pandalocker.services.visibility();
            
            var $wrap = $("<div class='bp-debugger-vc-wrap'></div>");
            
            for ( var i in visibility ) {
                var fitler = visibility[i];
                if ( !fitler.conditions ) continue;
                
                var $filter = $("<div class='bp-debugger-vc-filter'><div class='bp-filter-type'></div><div class='bp-filter-container'></div></div>");
                $filter.appendTo($wrap);
                
                var filterType = null;
                
                if ( fitler.type === 'showif' ) {
                    filterType = 'Показать замок ЕСЛИ';
                } else {
                    filterType = 'Скрыть замок ЕСЛИ';
                }
                   
                $filter.find('.bp-filter-type').html(filterType);
                
                if ( this.visibilityService.matchFilter(fitler) ) {
                    $filter.addClass('bp-passed');
                } else {
                    $filter.addClass('bp-notpassed');     
                }
                    
                for ( var n in fitler.conditions ) {
                    var scope = fitler.conditions[n];
                    if ( !scope.conditions ) continue;
                    
                    var $scope = $("<div class='bp-debugger-vc-scope'><div class='bp-vc-and'>and</div><div class='bp-scope-container'></div></div>");
                    $scope.appendTo($filter.find(".bp-filter-container"));
                    
                    if ( this.visibilityService.matchScope(scope) ) {
                        $scope.addClass('bp-passed');
                    } else {
                        $scope.addClass('bp-notpassed');     
                    }
                        
                    for ( var k in scope.conditions ) {
                        var condition = scope.conditions[k];
                        
                        var parameter = condition.param;
                        var type = condition.type || 'text';
                                    
                        var $condition = $("<div class='bp-debugger-vc-condition'><div class='bp-vc-or'>or</div><div class='bp-condition-container'></div></div>");
                        $condition.appendTo($scope.find(".bp-scope-container"));
                         
                        var html = null;
                        
                        var provider = this.visibilityService.getValueProvider( parameter );
                        if ( !provider ) {
                            html = '[Ошибка]: провайдер со значением "%s" не найдет.'.replace('%s', parameter);
                            $condition.html(html);
                            continue;
                        }

                        var currentValue = provider.getValue();
                        if ( currentValue === null ) {
                            html = '[Ошибка]: значение возвращаемое провайдером "%s" равно нулю(null).'.replace('%s', parameter);
                            $condition.html(html);
                            continue;
                        }
                        
                        currentValue = this.visibilityService.castValue( currentValue, type );
                        if ( type === 'date' ) currentValue = this.formatDate( currentValue );
                        
                        var html = '[{param}] {operator} {value} <span class="bp-current">current = <strong>{current}</strong></span>';
                        
                        html = html.replace('{param}', parameter);
                        html = html.replace('{operator}', this.getVcOperatorName( condition ) );
                        html = html.replace('{value}', this.getVcValue( condition ) );
                        html = html.replace('{current}', currentValue );
                        
                        $condition.find('.bp-condition-container').append(html);

                        if ( this.visibilityService.matchCondition(condition) ) {
                            $condition.addClass('bp-passed');
                        } else {
                            $condition.addClass('bp-notpassed');     
                        }
                    }
                }
            }
            
            var $description = $("<div class='bp-vc-description'>Ниже приведены условия, применяемые к этому замку.</div>");
            $wrap.prepend( $description );
            
            return $wrap;
        },
        
        getVcOperatorName: function( condition ) {
            var operator = condition.operator;
            
            if ( operator === 'equals' ) return '=';
            if ( operator === 'notequal' ) return '<>';
            if ( operator === 'greater' ) return '>';
            if ( operator === 'less' ) return '<';
            if ( operator === 'older' ) return '>';
            if ( operator === 'younger' ) return '<';
            if ( operator === 'contains' ) return 'содержит';
            if ( operator === 'notcontain' ) return 'не содержит';
            if ( operator === 'between' ) return 'между';
            return operator;
        },
        
        getVcValue: function( condition ) {
            
            var value = condition.value;
            var operator = condition.operator;
            var type = condition.type;
            
            var converToRange = (type === 'date' && (operator === 'equals' || operator === 'notequal'));
            value = this.visibilityService.castValue( value, type, converToRange  ? 'range' : null );

            if ( condition.operator === 'between' ) {
                if ( condition.type === 'date' ) {
                    return '<strong>' + this.formatDate( value.start ) + '</strong> and <strong>' + this.formatDate( value.end ) + '</strong>';
                } else {
                    return '<strong>' + value.start + '</strong> and <strong>' + value.end + '</strong>';
                }
            } else {
                if ( condition.type === 'date' ) {
                    if ( value.range ) {
                        return '<strong>' + this.formatDate( value.start ) + '</strong> and <strong>' + this.formatDate( value.end ) + '</strong>';
                    } else {
                        return '<strong>' + this.formatDate( value ) + '</strong>';  
                    }
                } else {
                    return '<strong>' + value + '</strong>';
                }
            }
        },
        
        formatDate: function( timestamp ) {
            var date = new Date(timestamp);
            
            var seconds = date.getSeconds();
            var minutes = date.getMinutes();
            var hours = date.getHours();
            
            var day = date.getDate();
            if ( day < 10 ) day = '0' + day;
            
            var month = date.getMonth();
            if ( month < 10 ) month = '0' + month;
            
            var year = date.getFullYear();
            
            return day + '.' + month + '.' + year + ' ' + hours + ':' + minutes + ':' + seconds;
        },
        
        explainStandartWay: function( identity, api ) {
            if ( !api || !api.options || !api.options.locker ) return false;
            
            var $wrap = $("<div class='bp-debugger-sv-wrap'>Или нажмите сюда: </div>");
            
            var $button = $("<a href='#' class='bp-debugger-btn'>Сбросить данные</a>");
            $button.appendTo($wrap);
            
            $button.click(function(){
 
                if ( window.localStorage ) {
                    for( var i=0, len = window.localStorage.length; i < len; i++) {
                        var key = window.localStorage.key(i);
     
                        if (/^(page|opanda)_[a-z0-9]+_hash/.test(key) ) {
                            window.localStorage.removeItem(key);
                        }                     
                    }
                }
                
                window.localStorage.removeItem('scope_global');
                
                var cookies = document.cookie.split(/;/);
                for (var i = 0, len = cookies.length; i < len; i++) {
                    var cookie = cookies[i].split(/=/);
                    if (/^(page|opanda)_[a-z0-9]+_hash/.test(key) ) {
                        $.pandalocker.tools.cookie(cookie[0], null);
                    }
                }
                
                $.pandalocker.tools.cookie('scope_global', null);
                
                window.location.reload();
                return false;
            });
            
            return $wrap;
        }
    };
    
    window.bizpanda.debugger.init();

})(jQuery);