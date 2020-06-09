/*global jQuery:false */
'use strict';
// jshint unused:vars

angular.module('RkwDomService', [])
.factory('DomService', [function () {
	
	var obj = {
		getPTagsFromStartToEnd: function(startContainer, endContainer) {
			var containers = [];
			var tempContainer = startContainer;
			containers.push(tempContainer);
			if(tempContainer !== endContainer) {
				do {
					tempContainer = tempContainer.nextSibling;
					containers.push(tempContainer);
				} while(tempContainer !== endContainer);
			}

			containers = containers.filter(function(item) {
				if(item.tagName === 'P') {
					return true;
				}
			});

			return containers;			
		}		
	};

	
	obj.getSelectionText = function() {
		var text = '';
	    if (window.getSelection) {
	        text = window.getSelection().toString();
	    } else if (document.selection && document.selection.type !== 'Control') {
	        text = document.selection.createRange().text;
	    }
	    return text;
	};

	obj.getSelectedPTags = function () {
		var selection = null;
		if(window.getSelection) {
			selection = window.getSelection();
		} else if(document.selection && document.selection.type !== 'Control') {
			selection = document.selection;
		}

		if(selection.rangeCount === 0) {
			return [];
		}
		var range = selection.getRangeAt(0);
		var start = range.startContainer.parentNode;
		var end = range.endContainer.parentNode;
		// up to P
		var i = 0;
		if(start !== null) {
			while(start.nodeName !== 'P' && i < 10) {
				start = start.parentNode;
				i++;
			}
		} else {
			return [];
		}
		
		// up to P
		i = 0;
		if(end !== null) {
			while(end.nodeName !== 'P' && i < 10) {
				end = end.parentNode;
				i++;
			}
		}
		
		var containers = obj.getPTagsFromStartToEnd(start, end);

		return containers;			
	};

	obj.markPTags = function(tags, markerClass) {
		tags.forEach(function(item) {
			if(jQuery.isArray(markerClass)) {
				jQuery.each(markerClass, function(index, cls) {
					jQuery(item).addClass(cls);
				});
			} else {
				jQuery(item).addClass(markerClass);				
			}
		});
	};

	return obj;
}]);
/*global jQuery:false */
'use strict';
// jshint unused:vars

/**
 * @ngdoc object
 * @name Pdf2Content Module
 * @module Pdf2Content
 * @description The Module which holds the application logic
 *
 * The Application with its dependencies
 */
var app = angular.module('Pdf2Content', [
    'ngRoute',
    'ng-context-menu',
    'ui.tree',
    'truncate',
    'RkwDomService',
    'uuid',
    'textAngular',
    'ui.bootstrap',
    'ui.keypress'
    //,
    //'ui.tinymce'
]);

angular.module('textAngular').config(function ($provide) {
    $provide.decorator('taOptions', ['taRegisterTool', 'taSelection', '$delegate', function (taRegisterTool, taSelection, taOptions) {
        // $delegate is the taOptions we are decorating
        // register the tool with textAngular
        taRegisterTool('clearBreaks', {
            iconclass: 'fa fa-eraser',
            tooltiptext: 'Entfernt alle <br> Tags im Text',
            action: function () {
                var textElement = this.$editor().displayElements.text;
                var sourceCode = textElement[0].innerHTML;
                textElement[0].innerHTML = '';
                var newCode = sourceCode.replace(/<br[^>]*>/gi, '');

                textElement[0].innerHTML = newCode;
                //taSelection.insertHtml(newCode);
            }
        });
        // add the button to the default toolbar definition
        taOptions.toolbar[1].push('clearBreaks');

        //debugger;
        taRegisterTool('clearHyphen', {
            iconclass: 'fa fa-minus',
            tooltiptext: 'Entfernt alle Trennungen durch Bindestriche im Text',
            action: function () {
                var textElement = this.$editor().displayElements.text;
                var sourceCode = textElement[0].innerHTML;
                textElement[0].innerHTML = '';

                var newCode = sourceCode.replace(/(\w+)-[\s(?:&#10;)]+(\w+)/gi, function(matched, $1, $2) {
                    return $1+$2;
                });

                textElement[0].innerHTML = newCode;
                //taSelection.insertHtml(newCode);
            }
        });
        // add the button to the default toolbar definition
        taOptions.toolbar[1].push('clearHyphen');




        return taOptions;

    }]);
});

app.filter('htmlToText', function () {
    return function (text) {
        return String(text).replace(/<[^>]+>/gm, '');
    };
});

app.controller('ModalChapterInstanceController', ['$scope', '$modalInstance', 'tree', function ($scope, $modalInstance, tree) {
    $scope.tree = tree;

    $scope.ok = function () {
        $modalInstance.close($scope.tree);
    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };

    $scope.rteConfig = [
        ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'pre', 'quote'],
        ['bold', 'italics', 'underline'],
        ['ul', 'ol', 'undo', 'redo', 'clear', 'clearBreaks', 'clearHyphen'],
        ['justifyLeft', 'justifyCenter', 'justifyRight', 'indent', 'outdent'],
        ['html', 'insertLink']
    ];
}]);

/**
 * @ngdoc function
 * @name Pdf2Content.MainController
 * @module Pdf2Content
 * @scope
 * @description The Main Controller
 */
app.controller(
    'MainController', [
        '$scope',
        '$filter',
        'DomService',
        'rfc4122',
        '$modal',
        function ($scope,
                  $filter,
                  DomService,
                  rfc4122,
                  $modal) {

            // The Current Target marked in the DOM
            $scope.currentTarget = null;

            // The marked Text before pressing context menu
            $scope.markedText = null;

            // The marked DOM Elements <p> before pressing context menu
            $scope.markedDOMElements = [];

            // The marked DOM Elements <p> used for text body in text element before pressing context menu
            $scope.markedTextElements = [];

            // The marked DOM Elements <p> used for text title in text element before pressing context menu
            $scope.markedTitleElements = [];

            // The Data for the tree component
            $scope.treeData = [];

            // The tree $scope, to fire functions on
            $scope.treeScope = null;

            // The tree element to be inserted, used for forms
            $scope.tree = {};

            // The focussed tree entry
            $scope.focusTreeEntry = null;

            // Property wether to show the create chapter form
            $scope.showCreateChapterForm = false;

            $scope.focussedTreeMenuItem = null;

            // Options for the tree component
            $scope.treeOptions = {
                // drag and drop rules
                accept: function (sourceNodeScope, destNodeScope) {
                    var sourceNode = sourceNodeScope.$parent.node;

                    if (sourceNode.type === 'element') {

                        // Root Node
                        if (destNodeScope.$nodeScope === null) {
                            return false;
                        }

                        if (destNodeScope.$nodeScope.$parent.node === 'element') {
                            return false;
                        }

                        return true;
                    }

                    if (sourceNode.type === 'chapter') {
                        if (destNodeScope.$nodeScope === null) {
                            return true;
                        }

                        if (destNodeScope.$nodeScope.$parent.node.type === 'element') {
                            return false;
                        }

                        return true;
                    }

                    return true;
                },

                // fired after dropping
                dropped: function (event) {
                    $scope.correctNumbering($scope.treeData, '');
                }
            };

            $scope.rteConfig = [
                ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'pre', 'quote'],
                ['bold', 'italics', 'underline'],
                ['ul', 'ol', 'undo', 'redo', 'clear', 'clearBreaks', 'clearHyphen'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'indent', 'outdent'],
                ['html', 'insertLink']
            ];


            // Control Chapter

            // * @ngdoc function
            // * @name Pdf2Content.MainController.addChapter
            // * @module Pdf2Content
            /**
             * Adds a Chapter to the temporary form object and shows the form
             *
             * @param {string} type The Subtype
             */
            $scope.addChapter = function (type) {
                // do nothing if menu is disabled
                if (!$scope.isMenuDisabled('addMainChapter')) {

                    if (type === 'main') {
                        $scope.tree.chapterNumber = $scope.treeData.length + 1;
                    } else {
                        $scope.tree.chapterNumber = $scope.focusTreeEntry.chapterNumber + '.' + ($scope.focusTreeEntry.nodes.length + 1);
                    }

                    $scope.tree.title = $scope.markedText;
                    $scope.tree.type = 'chapter';
                    $scope.tree.subtype = type;


                    var modalInstance = $modal.open({
                        templateUrl: 'modalCreateChapterForm.html',
                        controller: 'ModalChapterInstanceController',
                        size: 'lg',
                        windowClass: 'modal-backdropper',
                        backdrop: false,
                        resolve: {
                            tree: function () {
                                return $scope.tree;
                            }
                        }
                    });

                    modalInstance.result.then(function (tree) {
                        $scope.tree = tree;
                        $scope.createChapter();
                    }, function () {
                        // if rejected
                        $scope.hideForms();
                    });
                }
            };

            /**
             * Creates the chapter, fired after submitting the chapterform
             */
            $scope.createChapter = function () {
                var copy = angular.copy($scope.tree);
                copy.nodes = [];
                copy.collapsed = false;

                copy.uuid = rfc4122.v4();

                DomService.markPTags($scope.markedDOMElements, [$scope.getClassForNode(copy), copy.uuid]);

                if (copy.subtype === 'sub') {
                    // child of focussed node
                    $scope.focusTreeEntry.nodes.push(copy);

                } else if (copy.subtype === 'main') {
                    // Child of root
                    $scope.treeData.push(copy);
                }
                $scope.hideChapterForms();


                // reset Form Data
                $scope.tree = {};

                // activate new Chapter als active focussed chapter
                $scope.focusTreeEntry = copy;
            };
            // \Control Chapter


            // Control Text
            /**
             * Shows the element form, sets the temporary object
             */
            $scope.addElement = function () {
                // do nothing if menu is disabled
                if (!$scope.isMenuDisabled('addElement')) {

                    //$scope.showCreateElementForm = true;

                    $scope.tree.chapterNumber = $scope.focusTreeEntry.chapterNumber + '.' + ($scope.focusTreeEntry.nodes.length + 1);
                    //var trimmed = $scope.markedText.replace(/\n+/gi,'<br />');
                    //var trimmed = $scope.markedText.replace(/\n+/gi, '');
                    var trimmed = $scope.markedText;
                    //trimmed = trimmed.replace()

                    // transform Lernen-\nde to Lernende

                    $scope.tree.text = trimmed;
                    // $scope.tree.text = $scope.markedText;
                    $scope.tree.type = 'element';
                    $scope.markedTextElements = $scope.markedDOMElements;


                    var modalInstance = $modal.open({
                        templateUrl: 'modalCreateElementForm.html',
                        controller: 'ModalChapterInstanceController',
                        size: 'lg',
                        windowClass: 'modal-backdropper',
                        backdrop: false,
                        resolve: {
                            tree: function () {
                                return $scope.tree;
                            }
                        }
                    });

                    modalInstance.result.then(function (tree) {
                        $scope.tree = tree;
                        $scope.createElement();
                    }, function () {
                        // if rejected
                        $scope.hideForms();
                    });
                }
            };

            /**
             * Creates an element, hides the form, fired after submitting the form
             */
            $scope.createElement = function () {
                //debugger;
                var copy = angular.copy($scope.tree);
                copy.nodes = [];
                copy.collapsed = false;

                copy.uuid = rfc4122.v4();

                var markelements = $scope.markedTextElements.concat($scope.markedTitleElements);
                DomService.markPTags(markelements, ['markText', copy.uuid]);

                // DomService.markPTags($scope.markedDOMElements, ['markText', copy.uuid]);

                // child of focussed node
                $scope.focusTreeEntry.nodes.push(copy);

                $scope.hideForms();

                // reset Form Data
                $scope.tree = {};
            };
            // \Control Chapter

            // // Helper Functions
            // // Fired in the context menu, adds the marked text as title for the text element
            // $scope.addTextAsTitle = function(event) {
            // 	$scope.tree.title = $scope.markedText;
            // 	$scope.markedTitleElements = $scope.markedDOMElements;
            // };

            // // Fired in the context menu, adds the marked text as text for the text element
            // $scope.addTextAsText = function() {
            // 	var trimmed = $scope.markedText.replace(/\n+/g,'<br />');
            // 	$scope.tree.text = trimmed;
            // 	$scope.markedTextElements = $scope.markedDOMElements;
            // };
            // // \Helper Functions


            // Hides the forms, resets the temporary object
            $scope.hideForms = function () {
                $scope.tree = {};
                $scope.showCreateChapterForm = false;
                $scope.showCreateElementForm = false;
            };

            // Hides only the element form
            $scope.hideElementForms = function () {
                $scope.tree = {};
                $scope.showCreateChapterForm = false;
                $scope.showCreateElementForm = false;
            };

            // Hides only the chapter form
            $scope.hideChapterForms = function (type) {
                $scope.tree = {};
                $scope.showCreateChapterForm = false;
            };

            // Helper method for the text in the tree, uses a word filter
            $scope.elementText = function (node) {
                if (node.title !== '' && node.title !== undefined && node.title !== null) {
                    if (node.type === 'element') {
                        return node.title;
                    } else {
                        return $filter('words')(node.title, 10);
                    }
                }

                return $filter('words')(node.text, 10);
            };

            // Context Menu Call
            $scope.onShow = function (event) {
                $scope.currentTarget = event.target;
                if ($scope.currentTarget.nodeName !== 'P') {
                    $scope.markedText = DomService.getSelectionText();
                    if ($scope.markedText === '') {
                        $scope.markedText = $scope.currentTarget.textContent;
                    }
                } else {
                    $scope.markedText = DomService.getSelectionText();
                    if ($scope.markedText === '') {
                        $scope.markedText = $scope.currentTarget.textContent;
                    }
                }

                $scope.markedText = $scope.transformText($scope.markedText);

                var ptags = DomService.getSelectedPTags();

                if (ptags.length === 0) {
                    ptags = [$scope.currentTarget];
                }
                $scope.markedDOMElements = ptags;
            };

            // Context Menu on Close callback, not used
            $scope.onClose = function () {
            };
            // \Context Menu Call


            // Focus
            // checks if the node is in focus
            $scope.isNodeInFocus = function (node) {
                return $scope.focusTreeEntry === node;
            };

            // sets the node as focus node
            $scope.focusNode = function (node) {
                $scope.focusTreeEntry = node;
            };
            // \Focus

            // checks if an menu item is disabled or not
            $scope.isMenuDisabled = function (menuItem) {
                if (menuItem === 'addMainChapter') {
                    // disable if a form is open
                    return ($scope.showCreateElementForm || $scope.showCreateChapterForm);
                }

                var formOpen;
                if (menuItem === 'addSubChapter' || menuItem === 'addElement') {
                    formOpen = ($scope.showCreateElementForm || $scope.showCreateChapterForm);
                    return formOpen || $scope.treeData.length === 0;
                }

                if (menuItem === 'textHelper') {
                    formOpen = $scope.showCreateElementForm;
                    return !formOpen;
                }
            };


            // Tree Menu Callbacks

            $scope.onTreeMenuShow = function (node, treeScope) {
                $scope.focussedTreeMenuItem = node;
                $scope.treeScope = treeScope;
            };

            // \Tree Menu Callbacks

            // Tree Menu Functions
            // delete node function on the scope, removes classes from childs, corrects the numbering of chapters and removes the node
            $scope.deleteNode = function () {
                if ($scope.focussedTreeMenuItem !== null && $scope.focussedTreeMenuItem !== undefined) {
                    $scope.nodeDeleter($scope.focussedTreeMenuItem, true);
                }

                $scope.correctNumbering($scope.treeData, '');
                jQuery('.open').removeClass('open');
            };

            $scope.nodeDeleter = function (node, deleteAndUnmark) {
                // has Node childs, traverse down
                angular.forEach(node.nodes, function (item) {
                    $scope.nodeDeleter(item, false);
                });

                $scope.unmarkTags(node.uuid);
                if (deleteAndUnmark === true) {
                    $scope.treeScope.remove();
                    if ($scope.treeData.length > 0) {
                        $scope.focusNode($scope.treeData[0]);
                    }
                }
            };

            // removes the Classes from all items that have the same uuid class
            $scope.unmarkTags = function (uuid) {
                jQuery('.' + uuid).removeClass();
            };

            // \Tree Menu Functions

            // toggles the tree, uses the tree scope
            $scope.toggle = function (scope) {
                scope.toggle();
            };

            // builds the Chapter Number by a start value and an index
            $scope.buildChapterNumber = function (start, index) {
                if (start === '') {
                    return index;
                }
                return start + '.' + index;
            };

            // corrects the numbering of chapter numbers after deleting nodes or drag&drop
            $scope.correctNumbering = function (nodes, start) {
                if (arguments.length === 1) {
                    start = '';
                }
                angular.forEach(nodes, function (item, key) {
                    item.chapterNumber = $scope.buildChapterNumber(start, key + 1);
                    $scope.changeNodeClassInText(item);
                    $scope.correctNumbering(item.nodes, item.chapterNumber);
                });
            };

            $scope.scrollToNode = function (node) {
                var _formContainer = '.editor';
                var el = '.'+node.uuid;

                if ($(el).length > 0) {
                    var scrollTop = $(_formContainer).scrollTop();
                    var position = ($(el).offset().top - 160);
                    $(_formContainer).animate({
                        scrollTop: scrollTop + position
                    },1000);
                }
            };

            $scope.calcLevelForNode = function (node) {
                var arr = String(node.chapterNumber).split('.');
                var length = arr.length;
                if (length > 5) {
                    return 5;
                }
                return length;
            };

            $scope.getClassForNode = function (node) {
                if (node.type === 'element') {
                    return 'markText';
                }
                return 'markChapterLevel' + $scope.calcLevelForNode(node);
            };

            $scope.changeNodeClassInText = function (node) {
                var elements = jQuery('p.' + node.uuid);
                elements.removeClass();
                elements.addClass(node.uuid);
                elements.addClass($scope.getClassForNode(node));
            };

            $scope.addEmptyChapter = function (type) {
                $scope.markedDOMElements = [];
                $scope.markedText = '';
                $scope.addChapter(type);
            };

            $scope.transformText = function(text) {
                //text = text.replace(/(\w+)-[\s(?:&#10;)]+(\w+)/gi, function(matched, $1, $2) {
                //    return $1+$2;
                //});


                return text;
            };

        }]);