(function(){
    tinymce.PluginManager.add('code_block', function(editor, pluginUrl) {
        var $ = editor.$;
        var DOM = tinymce.dom.DOMUtils.DOM;

        function isCodeBlock(elm) {
            return elm && elm.nodeName == 'PRE' && elm.className.indexOf('language-') !== -1;
        }

        function trimArg(predicateFn) {
            return function(arg1, arg2) {
                return predicateFn(arg2);
            };
        }

        function getSelectedCodeBlock() {
            var node = editor.selection.getNode();

            if (isCodeBlock(node)) {
                return node;
            }

            return null;
        }

        function getShowLineNumbers() {
            var node = getSelectedCodeBlock();
            if (node) {
                return (node.className.indexOf('line-numbers') !== -1);
            }


            return false;
        }

        function getShowCommandPrompt() {
            var node = getSelectedCodeBlock();
            if (node) {
                return (node.className.indexOf('command-line') !== -1);
            }

            return false;
        }

        function getCommandPrompt() {
            var node = getSelectedCodeBlock();
            if (node) {
                return node.dataset.prompt;
            }

            return '';
        }

        function getCurrentCode() {
            var node = getSelectedCodeBlock();

            if (node) {
                return node.textContent;
            }

            return '';
        }

        function getCurrentLanguage() {
            var matches, node = getSelectedCodeBlock();

            if (node) {
                matches = node.className.match(/language-(\w+)/);
                return matches ? matches[1] : '';
            }

            return '';
        }

        function insertCodeBlock(data) {
            editor.undoManager.transact(function() {
                var node = getSelectedCodeBlock();

                var code = DOM.encode(data.code);

                var classes=['language-'+data.language];
                if (data.display_line_numbers) {
                    classes.push('line-numbers');
                }

                if (data.show_command_prompt) {
                    classes.push('command-line');
                }

                var cssClasses = classes.join(' ');

                if (node) {
                    editor.dom.setAttrib(node, 'class', cssClasses);
                    if (data.show_command_prompt)
                        node.dataset.prompt = data.command_prompt;

                    node.innerHTML = "<code>"+code+"</code>";
                    Prism.highlightElement(node);
                    editor.selection.select(node);
                } else {
                    editor.insertContent('<pre id="__new" class="' + cssClasses + '"><code>' + code + '</code></pre>');
                    editor.selection.select(editor.$('#__new').removeAttr('id')[0]);
                    if (data.show_command_prompt) {
                        node = getSelectedCodeBlock();
                        node.dataset.prompt = data.command_prompt;
                    }
                }
            });
        }

        function openEditor() {
            editor.windowManager.open({
                title: "Insert/edit code block",
                minWidth: Math.min(DOM.getViewPort().w, 800),
                minHeight: Math.min(DOM.getViewPort().h, 650),
                layout: 'flex',
                direction: 'column',
                align: 'stretch',
                body: [
                    {
                        type: 'listbox',
                        name: 'language',
                        label: 'Language',
                        maxWidth: 200,
                        value: getCurrentLanguage(),
                        values: codeBlockLanguages
                    },
                    {
                        type: 'checkbox',
                        name: 'display_line_numbers',
                        label: 'Display Line Numbers',
                        checked: getShowLineNumbers()
                    },
                    {
                        type: 'checkbox',
                        name: 'show_command_prompt',
                        label: 'Show Command Line',
                        checked: getShowCommandPrompt()
                    },
                    {
                        type: 'textbox',
                        name: 'command_prompt',
                        label: 'Command Prompt',
                        multiline: false,
                        spellcheck: false,
                        value: getCommandPrompt()
                    },
                    {
                        type: 'textbox',
                        name: 'code',
                        multiline: true,
                        spellcheck: false,
                        ariaLabel: 'Code view',
                        flex: 1,
                        style: 'direction: ltr; text-align: left',
                        classes: 'monospace',
                        value: getCurrentCode(),
                        autofocus: true
                    }
                ],
                onSubmit: function(e) {
                    insertCodeBlock(e.data);
                }
            });
        }

        editor.on('PreProcess', function(e) {
            $('pre[contenteditable=false]', e.node).
            filter(trimArg(isCodeBlock)).
            each(function(idx, elm) {
                var $elm = $(elm), code = elm.textContent;

                $elm.attr('class', $.trim($elm.attr('class')));
                $elm.removeAttr('contentEditable');

                $elm.empty().append($('<code></code>').each(function() {
                    // Needs to be textContent since innerText produces BR:s
                    this.textContent = code;
                }));
            });
        });

        editor.on('SetContent', function() {
            var unprocessedCodeBlocks = $('pre').filter(trimArg(isCodeBlock)).filter(function(idx, elm) {
                return elm.contentEditable !== "false";
            });

            if (unprocessedCodeBlocks.length) {
                editor.undoManager.transact(function() {
                    unprocessedCodeBlocks.each(function(idx, elm) {
                        $(elm).find('br').each(function(idx, elm) {
                            elm.parentNode.replaceChild(editor.getDoc().createTextNode('\n'), elm);
                        });

                        elm.contentEditable = false;
                        elm.innerHTML = "<code>"+editor.dom.encode(elm.textContent)+"</code>";
                        Prism.highlightElement(elm);
                        elm.className = $.trim(elm.className);
                    });
                });
            }
        });

        editor.addCommand('code_block', function(){
            openEditor();
        });

        editor.addButton('code_block', {
            title: "Code Block",
            cmd: "code_block",
            image: pluginUrl+'/../img/cb-edit-icon.png'
        });

        editor.on('dblClick', function(e){
           if (getSelectedCodeBlock() != null) {
               openEditor();
           }
        });
    });
})();