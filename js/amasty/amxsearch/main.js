/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2013 Amasty (http://www.amasty.com)
* @package Amasty_Xsearch
*/

var Xsearch = Class.create({
    timer: null,
    delay: 500,
    options: {},
    currentItem: null,
    clear: function(){
        if ($$('[id=am_search_container]').length > 0)
            $$('[id=am_search_container]').each(function(el){
                el.remove();
            })
    },
    initialize: function(options) {
        this.options = options;
        
        if ($$('input#search').length > 0){
            var _caller = this;
            var searchInput = $$('input#search')[0];
            
            searchInput.observe('click', function(){
                if (!searchInput.getAttribute('firstClick')){
                    searchInput.value = '';
                    searchInput.setAttribute('firstClick', 1)
                }
            });
            
            searchInput.observe('keyup', function(event){
                if ([37, 38, 39, 40, 9, 13].indexOf(event.keyCode) !== -1) // arrow keys, tab, enter
                    return;

                if (this.value.length >= _caller.options.minChars){
                    var q = this.value;
                    
                    if (_caller.timer != null){
                        clearTimeout(_caller.timer);
                    }
                    
                    _caller.timer = setTimeout(function(){
                        _caller.search.call(_caller, q);
                    }, _caller.delay)
                }
                
            });
            searchInput.observe('keydown', function(event){
                if ($('am_search_popup'))
                {
                    if ([38, 40, 9].indexOf(event.keyCode) !== -1)
                    {
                        if (this.currentItem == null)
                        {
                            this.currentItem = 0;
                        }
                        else
                        {
                            var totalItems = $$('#am_search_popup .am_element').length;

                            switch (event.keyCode)
                            {
                                case 38:
                                    this.currentItem = (this.currentItem + totalItems - 1) % totalItems;
                                    break;
                                case 40:
                                case 9:
                                    this.currentItem = (this.currentItem + 1) % totalItems;
                                    break;
                            }
                        }

                        $$('#am_search_popup .am_element').each(function(element, i){
                            if (i == this.currentItem)
                                element.addClassName('active');
                            else
                                element.removeClassName('active');
                        }.bind(this));
                        event.stop();
                    }
                    else if (event.keyCode == 13)
                    {
                        if (typeof this.currentItem == 'number')
                        {
                            var location = $('am_search_popup').down('a', this.currentItem).readAttribute('href');
                            window.location.href = location;

                        event.stop();
                    }
                }
                }
            }.bind(this));
//            $$('input#search')[0].observe('blur', function(){
//                clear();
//            })  
        }
        
        document.observe('click', function(){
            _caller.clear();
        });
    },
    loading: function(b){
        if (b)
            $('search').addClassName('loading')
        else 
            $('search').removeClassName('loading')
    },
    search: function(q){
        
        
        var _caller = this;
        _caller.loading(true);
        new Ajax.Request(_caller.options.url, {
            method:'get',
            requestHeaders: {Accept: 'application/json'},
            parameters: {q: q},
            onSuccess: function(response) {
                _caller.clear();
                
                var json = response.responseText.evalJSON(true)
                
                _caller.loading(false);
                
                if (json.items.length > 0){
                    
                    _caller.showPopup(json, q);
                }
                
//                    debugger;
            }
        });
        
    },
    hightlight: function(data, text){
        
        
        var words = text.split(' ');
        
        var reg = new RegExp('(' +words.join('|') + ')', "ig")
        data = data.replace(reg, "<span class=amhighlight>$1</span>")
        
        return data;
        
    },
    showPopup: function(data, text){
        this.currentItem = null;
        var container =  new Element('div');
        
        container.id = 'am_search_container';
        
        var mainContainer = new Element('div');
        mainContainer.id = 'am_search_popup';
        mainContainer.addClassName('am_search_popup')
        mainContainer.setStyle({
            'width': this.options.popupWidth + 'px'
        });
        
        var innerContainer = new Element('div');
        
        innerContainer.addClassName('am_search_popup_inner')
        
        var triangleContainer = new Element('div');
        triangleContainer.addClassName('am_search_popup_triangle')
        
        for (var order in data.items){
            
            var item = data.items[order];
            if (typeof(item) == 'object'){
                var element = new Element('div');
                
                element.addClassName('am_element');

                var elementWrapper = new Element('div');
                
                var link = new Element('a');
                
                link.setAttribute('href', item.url);
                
                
                var containerImage = new Element('div');
                containerImage.addClassName('am_image')
                
                var image = new Element('img');
                image.setAttribute('src', item.image)

                containerImage.appendChild(image);
                
                element.appendChild(elementWrapper)
                elementWrapper.appendChild(containerImage);
                
                
                var rightColumn = ['<div class=am_right>']
                rightColumn.push('<div class=am_title>', this.hightlight(item.name, text), '</div>');
                if (item.reviews != ''){
                    rightColumn.push('<div class=amreviews>', item.reviews, '</div>');
                }
                rightColumn.push(this.hightlight(item.description, text));
                rightColumn.push('<br/>', item.price);
                rightColumn.push('</div>')
                
                elementWrapper.innerHTML += rightColumn.join('');
                
                link.appendChild(element)
                
                innerContainer.appendChild(link);
            }
            
        }
        
        if (data.bottomHtml){
            var bottomHtml = new Element('div');
            bottomHtml.innerHTML = data.bottomHtml;
            innerContainer.appendChild(bottomHtml);
        }
        
        
        
        mainContainer.appendChild(innerContainer);
        
        container.appendChild(triangleContainer);
        container.appendChild(mainContainer);
        
        

        $('search_mini_form').appendChild(container);
        
//        this.initEvents(mainContainer);
        
        
    }, 
    initEvents: function(container){
        var maxDelay = 500;
        var delay = 0;
        var iterations = [];
        
        function clear(){
            for(var ind in iterations){
                var iteration = iterations[ind];
                if (typeof(iteration) == 'function'){
                    window.clearTimeout(iteration);
                }
            }
            iterations = [];
            delay = 0;
        }
        
        container.observe('mouseover', function(event) {
            
            clear();
            
        });
        
        
        
        container.observe('mouseout', function(event) {
            
            var target = Event.element(event);
            
            if (target == container){
                function iteration(){
                    delay += 100;

                    if (delay >= maxDelay){
                        clear();
                        container.remove();


                    } else {
                        var handler = window.setTimeout(function(){
                            iteration();
                       }, 100);

                       iterations.push(handler);
                    }
                }

                iteration();
            }
            
            
        });
    }
});
