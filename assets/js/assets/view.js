/*var locMarkers;
var locBounds;
var locMap;*/

var photographer = {
    assignAsset: function(assetId, photographerId) {
        return $.post(globals.ajaxurl+'photographer.php', {
            _mode:'asset',
            _task:'assign',
            _aid: assetId,
            _id: photographerId
        }, null, 'json');
    },
    editForm:function(asset_id) {
            return $.post(globals.ajaxurl + 'photographer.php', {
                    _mode:'asset',
                    _task:'get',
                    _aid:asset_id
            }, null, 'html');
    }
}

var group = {
    deleteAsset: function(assetId, groupId) {
        return $.post(globals.ajaxurl + 'groups.php', {
            '_mode':'asset',
            '_task':'del',
            'aid': assetId,
            'gid': groupId
        }, null, 'json');
    },
    approve: function(assetId) {
        return $.post(globals.ajaxurl + 'groups.php', {
            '_mode':'appr',
            'id':assetId
        }, null, 'json');
    },
    getAvailable:function(asset_id) {
        return $.get(globals.ajaxurl + 'groups.php', {
            'asset_id':asset_id,
            _task:'available'
        }, null, 'html');
    },
    assignAsset:function(asset_id, group_id) {
        return $.post(globals.ajaxurl + 'groups.php', {
            _mode:'asset',
            _task:'assign',
            _aid:asset_id,
            _gid:group_id
        }, null, 'json');
    },
    filter: function(terms, asset_id) {
        return $.post(globals.ajaxurl + 'groups.php', {
            _task:'search',
            qry:terms,
            'asset_id':asset_id
        }, null, 'html');
    },
    new: function(assetId, grpTitle) {
        return $.post(globals.ajaxurl + 'groups.php', {
            aid: assetId,
            _mode:'asset',
            _task:'add',
            title: grpTitle
        }, null, 'json')
    }
};

var assets = {
    setFeatured: function(assetId, isIt) {
        return $.post(globals.ajaxurl+'asset.php', {
            _mode:'feature',
            aid: assetId,
            feat: isIt
        }, null, 'json');
    },
    selects: function(assetId) {
        return $.post(globals.ajaxurl + 'asset.php', {
            '_mode':'selects',
            'id': assetId
        }, null, 'json');
    },
    outtake: function(assetId) {
        return $.post(globals.ajaxurl + 'asset.php', {
            '_mode':'outtake',
            'id': assetId
        }, null, 'json');
    },
    externalRestriction: function(assetId, reason) {
        return $.post(globals.ajaxurl + 'asset.php', {
            '_mode':'ext',
            'id': assetId,
            'rsn': reason
        }, null, 'json');
    },
    internalRestriction: function(assetId, reason) {
        return $.post(globals.ajaxurl + 'asset.php', {
            '_mode':'int',
            'id': assetId,
            'rsn': reason
        }, null, 'json');
    },
    subjectRestriction: function(assetId, reason) {
        return $.post(globals.ajaxurl + 'asset.php', {
            '_mode':'subj',
            'id':assetId,
            'rsn':reason
        }, null, 'json');
    },
    approve: function(assetId) {
        return $.post(globals.ajaxurl + 'asset.php', {
            '_mode':'appr',
            'id':$(ele).val()
        }, null, 'json');
    },
    getGroups: function(assetId) {
        return $.post(globals.ajaxurl + 'assetGroupsList.php', {
            id: assetId
        }, null, 'html');
    },
    setHippa: function(assetId) {
        return $.post(globals.ajaxurl + 'asset.php', {
            _mode:'hippa',
            id: assetId
        }, null, 'json');
    },
    setNcaa: function(assetId) {
        return $.post(globals.ajaxurl + 'asset.php', {
            _mode:'ncaa',
            id: assetId
        }, null, 'json');
    }
};

$(function() {
	/*locMap = L.map('locMap').setView([38.8, -99.3], 3);
	L.tileLayer('http://{s}.tile.cloudmade.com/c9db6f9425b84e1689464b841c69072f/997/256/{z}/{x}/{y}.png', {
		attribution:'Map data &copy;',
		maxZoom:18
	}).addTo(locMap);
	
	locMarkers = new L.LayerGroup().addTo(locMap);
	
	locBounds = new L.LatLngBounds();*/
	
	/**
	 * prompt for saving image data
	 */
	/*$('#saveMetaDialog').dialog({
		autoOpen:false,
		modal:true,
		'title':'Saving...'
	});*/
	
	/**
	 * Check for a shoot name, and if exists populate it for the shoot editor
	 */
	$.when(updateEditForm($.url.param('id'))).done(function(a) {
		$('#editShootFormHolder').html(a);
	});
	
        /**
         * 
         * load available groups for the asset
         */
        $('#avlSets').block({message:'loading...'});
        
        $.when(group.getAvailable($.url.param('id'))).done(function(a) {
            $('#avlSets').unblock();
            $('#availSets').html(a);
        });
        
        $(document).on('blur', '#filterAvlSets', function(e) {
            if($(e.currentTarget).val() == '') {
                $('#avlSets').block({message:'loading...'});
                
                $(e.currentTarget).val('Search');
                
                $.when(group.getAvailable($.url.param('id'))).done(function(a) {
                    $('#avlSets').unblock();
                    
                    $('#availSets').html(a);
                });
            } else {}
        });
        
        $(document).on('focus', '#filterAvlSets', function(e) {
            $(e.currentTarget).val('');
        });
        
        /*$('#filterAvlSets').autocomplete({
            source: function(request, response) {
                $('#avlSets').block({message:'loading...'});
                
                $.when(group.filter(request.term, $.url.param('id'))).done(function(a) {
                    $('#avlSets').unblock();
                    
                    $('#availSets').html(a);
                });
            },
            minLength: 2
        });*/
        
        $(document).on('click', '#resetFilterSets', function(e) {
            e.preventDefault();
            
            if($('#filterAvlSets').val() != '') {
                $('#avlSets').block({message:'loading...'});
                
                $('#filterAvlSets').val('Search');

                $.when(group.getAvailable($.url.param('id'))).done(function(a) {
                    $('#avlSets').unblock();
                    
                    $('#availSets').html(a);
                });
            }
        });
        
	/**
	 * Assign a photo shoot to an asset
	 */
	//var validator = $('#shootForm').validate();
	/*$("#shootWin").dialog({
		width:500,
		height:425,
		'autoOpen':false,
		'modal':true,
		'create':function(evt, ui) {},
		'title':'Shoot Assignment',
		'buttons':[
					{
					   'text':'Remove assignment',
					   'click':function() {
						   $.when(removeShoot($.url.param('id')), updateEditForm($.url.param('id'))).done(function(a, b) {
							   $('#shootNameTxt').html('');
							   $('#editShootFormHolder').html('');
							   $('#shootWin').dialog('close');
							   $('#editShootFormHolder').html(b[0]);
						   });
					   }
					},
		           {'text':'Cancel', 'click':function() {
		        	   $(this).dialog("close");
		        	   
		        	   $("#shootForm").find("textarea, input[type='text']").val("");
		        	   validator.resetForm();
		           }}
		           ]
	});*/
	
	$('#addShootBtn').on('click', function(e) {
		if($('#shootForm').valid()) {
			$.when(addShoot($('#shootForm').serializeJSON()), updateEditForm($.url.param('id'))).done(function(a, b) {
				$('#shootNameTxt').html(a[0].data.shoot_title);
				
				//$("#shootWin").dialog("close");
								
				$("#shootForm").find("textarea, input[type='text']").val("");
				
				$('#editShootFormHolder').html(b[0]);
				
				//validator.resetForm();
			});
   		}
	});
	
	//var eValidator = $('#editShootForm').validate();
	$('#editShootBtn').on('click', function(e) {
		if($('#editShootForm').valid()) {
			$.post(globals.ajaxurl+'shoots.php', $('#editShootForm').serializeJSON(), function(d) {
				$('#shootNameTxt').html(d.data.editShootTitle);
				
				//$("#shootWin").dialog("close");
				
				//eValidator.resetForm();
			}, 'json');
		} else {
			alert('Form is not valid');
		}
	});
	
	/*$('#orgWin').dialog({
		modal:true,
		'title':'Organization Manager',
		autoOpen:false,
		buttons:[
		         {'text':'Cancel', 'click':function() {
		        	 $(this).dialog('close');
		         }}
		         ],
		close:function(evt, ui) {
			 var asset_id = $('#assetId').val();
       	 
	       	 $.when(getOrgsString(asset_id)).done(function(a) {
	       		 $('#orgInfo').html(a.data);
	       	 });
		}
	});*/
	
	//$('#photogForm').validate();
	/*$('#photogWin').dialog({
		'modal':true,
		'title':'Photographer Assignment',
		'autoOpen':false,
		'buttons':[
		           {'text':'Cancel', 'click':function() {
		        	   $(this).dialog("close");
		           }}
		           ],
		'open':function(evt, ui) {
			$('#photogTabs').tabs('select', 0);
		}
	});*/
	
	//$('#addnInfo').tabs();
	
	/*$('#shootTabs').tabs({
		select:function(evt, ui) {
			if($(ui.tab).attr('href') == '#selectShootTab') {
				$('#shootsList').load(globals.ajaxurl + 'shoots.php', {_mode:'list'});
			}
		}
	});*/

    /**
     * Shoot info tabs
     */
    $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
        console.log('Activated tab: ' + e.target);
        console.log('Previosly active tab: ' + e.relatedTarget);
    });
	
	$(document).on('click', '.selectShoot', function(e) {
		e.preventDefault();
		
		$.when(assignShoot($(this).siblings('.shootId').val(), $.url.param('id')), updateEditForm($.url.param('id'))).done(function(a, b) {
			$('#shootNameTxt').html(a[0].data.title);
			
			$('#editShootFormHolder').html(b[0]);
			
			//$("#shootWin").dialog("close");
		});
	});
	
	/**
	 * list all groups assigned to this asset
	 */
	$("#groupsList").load(globals.ajaxurl + 'assetGroupsList.php', {'id':$("#assetId").val()});
	
	/**
	 * Existing groups search to add to asset
	 */
	/*$("#imgAddGrp").autocomplete({
		'source': function(request, response) {
			$.post(globals.ajaxurl + 'groupAutocomplete.php', {
				'query': request.term,
				'_aid':$.url.param('id')
			}, function(d) {
				response($.map(d, function(item) {
                                    return {
                                            'label': item.title,
                                            'value': item.id
                                    };
				}));
			}, "json");
		},
		'minLength': 2,
		'select': function(event, ui) {
			$.post(globals.ajaxurl + 'addAssetToGroup.php', {
				'aid': $("#assetId").val(),
				'gid': ui.item.value
			}, function(d1) {
				$("#groupsList").load(globals.ajaxurl + 'assetGroupsList.php', {
					'id': $("#assetId").val()
				}, function(d2) {
					$("#imgAddGrp").val("");
				});
			}, 'json');
			
			return false;
		}
	});*/
	
	/**
	 * Add a new group and assign to this asset
	 */
    $(document).on('click', '#newAstGrpBtn', function(e) {
        $.when(group.add($('#assetId').val(), $("#newGrpTitle").val())).done(function(a) {
            if(a.status == true) {
                $.when(assets.getGroups(a.data.asset_id)).done(function(b) {
                    $('#newGrpTitle').val('');
                    $('$groupsList').html(b);
                });
            }
        });
    });
	
	/**
	 * Hippa restriction checkbox
	 */
    $(document).on('change', '#hippaRestr', function(e) {
        var _this = this;

        $.when(assets.setHippa($(_this).val())).done(function(a) {
            if(a.data === 0) {
                $(_this).prev('#hippaStat').html('No');
            } else {
                $(_this).prev('#hippaStat').html('Yes');
            }
        });
    });
	
	/**
	 * NCAA restriction checkbox
	 */
    $(document).on('change', '#ncaaRestr', function(e) {
        var _this = this;

        $.when(assets.setNcaa($(_this).val())).done(function(a) {
            if(a.data === 0) {
                $(_this).prev("#ncaaStat").html("No");
            } else {
                $(_this).prev("#ncaaStat").html("Yes");
            }
        });
    });
	
	/**
	 * Group approval function
	 */
    $(document).on('change', '.chkGrpApproval', function(e) {
        var _this = this;

        $.when(group.approve($(_this).val())).done(function(a) {
            if(a === 0) {
                $(_this).prev('.grpStatus').html("Not active");
            } else {
                $(_this).prev('.grpStatus').html("Active");
            }
        });
    });
	
	/**
	 * Asset approval function
	 */
    $(document).on('change', '#chkAssetAppr', function(e) {
        var _this = this;

        $.when(assets.approve($(_this).val())).done(function(a) {
            if(a === 0) {
                $(_this).prev('#astStatus').html("Not Active");
            } else {
                $(_this).prev('#astStatus').html("Active");
            }
        });
    });

    $(document).on('blur', '#subjRsn', function(e) {
        var assetId = $(this).prev('#assetId').val();
        var reason = $(this).val();

        $.when(assets.subjectRestriction(assetId, reason)).done(function(a) {
            if(a.data.activity === 0) {
                $("#subjStat").attr('class', 'restrStatNo').html('No');
            } else {
                $("#subjStat").attr('class', 'restrStatYes').html('Yes');
            }
        });
    });

    $(document).on('blur', '#internalRsn', function(e) {
        var assetId = $(this).prev('#assetId').val();
        var reason = $(this).val();

        $.when(assets.internalRestriction(assetId, reason)).done(function(a) {
            if(a.data.activity === 0) {
                $("#internalTxt").attr('class', 'restrStatNo').html('No');
            } else {
                $("#internalTxt").attr('class', 'restrStatYes').html('Yes');
            }
        });
    });

    $(document).on('blur', '#externalRsn', function(e) {
        var assetId = $(this).prev('#assetId').val();
        var reason = $(this).val();

        $.when(assets.externalRestriction(assetId, reason)).done(function(a) {
            if(a.data.activity === 0) {
                $("#externalTxt").attr('class', 'restrStatNo').html('No');
            } else {
                $("#externalTxt").attr('class', 'restrStatYes').html('Yes');
            }
        });
    });
	
	/**
	 * Handle asset outtake status
	 */
    $(document).on('change', '#chkAssetOtk', function(e) {
        var assetId = $(this).val();
        var ele = this;

        $.when(assets.outtake(assetId)).done(function(a) {
            if(a === 0) {
                $(ele).prev("#astOuttake").html("No");
            } else {
                $(ele).prev("#astOuttake").html("Yes");
            }
        });
    });
	
	/**
	 * Handle asset selects status
	 */
    $(document).on('change', '#chkAssetSlct', function(e) {
        var ele = this;
        var assetId = $(this).val();

        $.when(assets.selects(assetId)).done(function(a) {
            if(a === 0) {
                $(ele).prev("#astSelect").html("No");
            } else {
                $(ele).prev("#astSelect").html("Yes");
            }
        });
    });
	
	/*$('#txtAstPubd').autocomplete({
		'source':function(req, res) {
			$.post(globals.ajaxurl + 'pubs.php', {'_mode':'search', 'query':req.term}, function(d) {
				res($.map(d.data, function(item) {
					return {'label':item.title, 'value':item.id};
				}));
			}, 'json');
		},
		'focus':function(evt, ui) {},
		'minLength':2,
		'select':function(evt, ui) {
			$('#txtAstPubd').val(ui.item.label);
			
			return false;
		}
	});*/
	
	$('#txtAstPubd').blur(function() {
		procPubRestriction($(this).val(), $('#dateAstPubd').val());
	});
	
	/*$('#dateAstPubd').datepicker({
		changeMonth:true,
		changeYear:true,
		yearRange:'2000:'+maxDate.getFullYear(),
		'onSelect':function(date, obj) {
			procPubRestriction($('#txtAstPubd').val(), date);
		}
	});*/
	
	/**
	 * Delete group from asset assignment
	 */
    $(document).on('click', '.delAstGrp', function(e) {
        var assetId = $.url.param('id');
        var groupId = $(this).prop('id');

        $.when(group.deleteAsset(assetId, groupId)).done(function(a) {
            $.when(assets.getGroups(a.data.asset_id)).done(function(b) {
                $('#groupsList').html(b);

                /**
                 *
                 * load available groups for the asset
                 */
                $.when(group.getAvailable($.url.param('id'))).done(function(a) {
                    $('#availSets').html(a);
                });
            });
        });
    });
	
	/**
	 * edit assets' title
	 */
	$("#titleTxt").editable(globals.ajaxurl + 'asset.php', {
		'submitdata': {
			'_mode':'edit',
			'_task':'title',
			'aid':$.url.param('id')
		}, 'callback': function(val, set) {
			$(this).html(val);
		},
		'tooltip':'Click to edit title...',
		'indicator':'Saving...',
		'submit':'Save',
		'cancel':'Cancel',
		'style':'display:inline',
		'height':20,
		'width':200
	});
        
    $('#creditTxt').editable(globals.ajaxurl+'asset.php', {
        'submitdata':{
            '_mode':'edit',
            '_task':'credit',
            'aid':$.url.param('id')
        },
        'callback':function(val, set) {
            $().html(val.data.value);
        },
        'tooltip':'Click to change asset credit',
        'indicator':'Saving...',
        'submit':'Save',
        'cancel':'Cancel',
        'style':'display:inline',
        'width':150,
        'height':20
    });
	
    $('.datePrompt').datepicker();
	
	
	$("#assignShoot").click(function() {
		$("#shootWin").dialog("open");
	});
	
	$("#editShoot").click(function() {
		$('#changeShoot').dialog("open");
	});
	
	/**
	 * Keywords
	 */
	$('#assetWords').tagit({
		'delay':100,
		'allowSpaces':true,
		'caseSensitive':true,
		'minLength':2,
		'tagSource':function(req, res) {
			$.post(globals.ajaxurl + 'keywords.php', {'_mode':'suggest', 'q':req.term}, function(d) {
				res($.map(d.data, function(item) {
					return {
						'label':item.keyword,
						'value':item.keyword
					};
				}));
			}, 'json');
		},
		'onTagAdded':function(evt, tag) {
			var kword = $(tag).find('span.tagit-label').text();
			$.post(globals.ajaxurl + 'keywords.php', {'_mode':'asset', '_task':'add', '_tag':kword, '_id':$.url.param('id')}, function(d) {}, 'json');
		},
		'onTagRemoved':function(evt, tag) {
			var kword = $(tag).find('span.tagit-label').text();
			$.post(globals.ajaxurl + 'keywords.php', {'_mode':'asset', '_task':'del', '_tag':kword, '_id':$.url.param('id')}, function(d) {}, 'json');
		}
	});
	
	var future = new Date();
	future.setDate(future.getFullYear()+5);
	$("#embgoDate").datetimepicker({
		'onSelect': function(dateTxt, inst) {
			if($("#embgoTime").val() != "") {
				$.post(globals.ajaxurl + 'asset.php', {
					'_mode':'embargo',
					'id':$.url.param('id'),
					'_date':dateTxt
				}, function(d) {
					$("#embgdTxt").attr('class', (isEmbargoed()) ? 'restrStatYes' : 'restrStatNo').html((isEmbargoed()) ? "Yes" : "No");
				}, 'json');
			}
		},
		'hourGrid':6,
		'minuteGrid':15,
		'minuteText':'Min.',
		'ampm':true,
		changeMonth:true,
		changeYear:true,
		yearRange:maxDate.getFullYear()+':'+future.getFullYear()
	});
	
	$("#embgoDate").blur(function() {		
		if(isEmbargoed() == false) {
			$.post(globals.ajaxurl + 'asset.php', {
				'_mode':'embargo',
				'id':$.url.param('id'),
				'_date':$("#embgoDate").val()
			}, function(d) {
				$("#embgdTxt").attr('class', (isEmbargoed()) ? 'restrStatYes' : 'restrStatNo').html((isEmbargoed()) ? "Yes" : "No");
			}, 'json');
		}
	});
        
	$('#dateCreated').datepicker({
		changeMonth:true,
		changeYear:true,
		yearRange:'1960:'+maxDate.getFullYear(),
		'onSelect':function(date, inst) {
			$.post(globals.ajaxurl + 'asset.php', {
				'_mode':'edit',
				'_task':'created',
				'_id':$.url.param('id'),
				'_date': date
			}, function(d) {
				$('#dateCreated').val(date);
			}, 'json');
		},
                beforeShow: function() {
                    $('#dateCreated').attr('disabled', 'disabled');
                },
                onClose:function() {
                    $('#dateCreated').removeAttr('disabled');
                }
	});

	$('#changePhotog, #assignPhotog').click(function() {
		//$('#photogWin').dialog("open");
	});
	
	/*$('#photogTabs').tabs({
		select:function(evt, ui) {
			if($(ui.tab).attr('href') == '#editPhotogTab') {
				var asset_id = $.url.param('id');
				
				$.when(photographer.editForm(asset_id)).done(function(a) {
					$('#editPhotogTab').html(a);
				});
			}
			
			if($(ui.tab).attr('href') == '#selectPhotogTab') {
				$('#photogsList').load(globals.ajaxurl+'photographer.php', {_mode:'all'});
			}
		}
	});*/

    $(document).on('click', '.selectPhotog', function(e) {
        var assetId = $.url.param('id');
        var photographerId = $(this).siblings('.photogId').val();

        $.when(photographer.assignAsset(assetId, photographerId)).done(function(a) {
            $('#photographer').html(a.data.firstname+' '+a.data.lastname);

            //$('#photogWin').dialog('close');
        });
    });
	
	//var validAddPhotog = $('#addPhotogForm').validate();
    $(document).on('click', '#addPhotogBtn', function(e) {
        //if($('#addPhotogForm').valid()) {
            $.post(globals.ajaxurl+'photographer.php', $('#addPhotogForm').serializeJSON(), function(d) {
                $('#photographer').html(d.data.addPhotogFname+' '+d.data.addPhotogLname);

                //$('#photogWin').dialog('close');

                $('#addPhotogForm').find('input').val('');

                //validAddPhotog.resetForm();
            }, 'json');
        //}
    });
	
	//var validateEditPhotog = $('#editPhotogForm').validate();
    $(document).on('click', '#editPhotogBtn', function(e) {
        //if($('#editPhotogForm').valid()) {
            $.post(globals.ajaxurl+'photographer.php', $('#editPhotogForm').serializeJSON(), function(d) {
                $('#photographer').html(d.data.editPhotogFname+' '+d.data.editPhotogLname);

                //$('#photogWin').dialog('close');
            }, 'json');
        //}
    });
	
	//////////////////////////////////////
	// functions
	//////////////////////////////////////
	function isEmbargoed() {
		if($("#embgoDate").val() != "") {
			return true;
		} else {
			return false;
		}
	}
	
	$('#finalCapn').editable(globals.ajaxurl + 'asset.php', {
		'submitdata': {
			'_mode':'edit',
			'_task':'capn',
			'_type':'final',
			'aid':$.url.param('id')
		},
		'callback':function(val, set) {
			//$(this).html(val);
		},
		'onblur':'submit',
		'type':'textarea',
		'tooltip':'Click to change caption',
		'indicator':'Saving...',
		'submit':'Save',
		'cancel':'Cancel',
		'style':'',
		'width':800,
		'height':230
	});
	
	$('#featCapn').editable(globals.ajaxurl + 'asset.php', {
		'submitdata': {
			'_mode':'edit',
			'_task':'capn',
			'_type':'feat',
			'aid':$.url.param('id')
		},
		'onblur':'submit',
		'callback':function(val, set) {
			//$(this).html(val.data.value);
			//console.log(val);
		},
		'type':'textarea',
		'tooltip':'Click to change caption',
		'indicator':'Saving...',
		'submit':'Save',
		'cancel':'Cancel',
		'style':'',
		'width':285,
		'height':230
	});
	
	$('#genCapn').editable(globals.ajaxurl + 'asset.php', {
		'submitdata': {
			'_mode':'edit',
			'_task':'capn',
			'_type':'gen',
			'aid':$.url.param('id')
		},
		'onblur':'submit',
		'callback':function(val, set) {
			//$(this).html(val.data.value);
		},
		'type':'textarea',
		'tooltip':'Click to change caption',
		'indicator':'Saving...',
		'submit':'Save',
		'cancel':'Cancel',
		'style':'',
		'width':285,
		'height':230
	});
	
	$('#copyrightTxt').editable(globals.ajaxurl + 'asset.php', {
		submitdata:{
			_mode:'edit',
			_task:'copyright',
			'aid':$.url.param('id')
		},
		onblur:'submit',
		tooltip:'Click to edit',
		indicator:'Saving...',
		submit:'Save',
		cancel:'Cancel',
		width:150,
		height:20
	});
	
	$('#txtNewKword').keydown(function(e) {
		if(e.which == 13) {
			procNewKeyword($(this).val(), $.url.param('id'));
		}
	});
	
	$('#btnNewKword').click(function() {
		procNewKeyword($('#txtNewKword').val(), $.url.param('id'));
	});
	
	function procNewKeyword(keyword, asset_id) {
		if(keyword != "") {
			$.post(globals.ajaxurl + 'keywords.php', {'_mode':'new', 'kword':keyword, 'aid':asset_id}, function(d) {
				$('#assetWords').tagit("createTag", keyword);
				$('#txtNewKword').val("");
			}, 'json');
		}
	}
	
	/**
	 * Handles adding a published status to a group of assets
	 * 
	 * @param pub_name
	 * @param date
	 */
	function procPubRestriction(pub_name, date) {
		if(pub_name != "" && date != "") {
			$.post(globals.ajaxurl + 'asset.php', {
				'_mode':'pub',
				'_task':'yes',
				'aid':$.url.param('id'),
				'pub_name':pub_name,
				'date':date
			}, function(d) {
				$('#chkAssetPub').attr({'checked':'checked'});
				$('#astPubbed').html('Yes');
			}, 'json');
		} else {
			$.post(globals.ajaxurl + 'asset.php', {
				'_mode':'pub',
				'_task':'no',
				'aid':$.url.param('id')
			}, function(d) {
				$('#chkAssetPub').removeAttr('checked');
				$('#astPubbed').html('No');
			}, 'json');
		}
	}
	
	$('#apprvAsset').click(function() {
		var ele = this;
		var status = $('#apprvStatus').val();
		
		if(status == 1) {
			$.post(globals.ajaxurl + 'asset.php', {'_mode':'approval', '_task':'no', 'asset':$.url.param('id')}, function(d) {
				$(ele).attr('title', 'Make public!');
				$(ele).children('img').attr({'src':globals.relurl + 'assets/images/thumbsdown.gif', 'alt':'Make public!'});
				$('#apprStatus').html('Private');
				$('#apprvStatus').val(0);
			}, 'json');
		} else {
			$.post(globals.ajaxurl + 'asset.php', {'_mode':'approval', '_task':'yes', 'asset':$.url.param('id')}, function(d) {
				$(ele).attr('title', 'Make private!');
				$(ele).children('img').attr({'src':globals.relurl + 'assets/images/thumbsup.gif', 'alt':'Make private!'});
				$('#apprStatus').html('Public');
				$('#apprvStatus').val(1);
			}, 'json');
		}
	});
        
    $('#assetAcc').accordion();
    
    $('#saveMeta').click(function() {
    	var asset_id = $.url.param('id');
    	
    	$('#saveMetaDialog').dialog('open');
    	
    	$.post(globals.ajaxurl+'asset.php', {_mode:'meta', _task:'update', 'asset':asset_id}, function(d) {
    		$('#saveMetaDialog').dialog('close');
    	}, 'json');
    });

    $(document).on('click', '#setFeature', function(e) {
        var asset = $.url.param('id');
        var isFeat = $(this).siblings('#hdnFeatStatus').val();

        $.when(assets.setFeatured(asset, isFeat)).done(function(a) {
            if(a.status === true) {
                if(a.data.isactive === 0) {
                    alert("Sorry, but you cannot feature a non-public image. Please, make the image public, and then set it as featured.");
                } else {
                    if(a.data.postdata.feat === 'No') {
                        $('#setFeature').children('img').attr({'src':globals.relurl+'assets/images/feature.png', 'alt':''});
                        $('#featuredStatus').html('Featured');
                    }
                }
            }
        });
    });
    
    $.when(getLoc($.url.param('id'))).done(function(a) {
    	if(a.status != false) {
    		/*locMarkers.clearLayers();
    		
    		var marker = new L.Marker(new L.LatLng(a.data.lat, a.data.lng));
    		locMarkers.addLayer(marker);
    		
    		locMap.setView(new L.LatLng(a.data.lat, a.data.lng), 13, true);
    		
    		$('<div />').attr({'class':'locListItem', 'data-lat':a.data.lat, 'data-lon':a.data.lng}).html(a.data.location).appendTo('#locList');*/
    	}
    });
    
    $('#locSearch').on('blur', function(e) {
    	if($(this).val() == '') {
    		$(this).val('Search for location...');
    	}
    });
    
    $('#locSearch').on('focus', function(e) {
    	$(this).val('');
    });
    
    $(document).on('click', '.locListItem', function(e) {
    	var locTxt = $(this).text();
    	var lat = $(this).attr('data-lat');
    	var lng = $(this).attr('data-lon');
    	
    	/*var latLng = new L.LatLng(lat, lng);
    	locMarkers.clearLayers();
    	
    	var locMarker = new L.Marker(latLng);
    	
    	
    	$.when(updateAssetLoc($.url.param('id'), latLng, locTxt)).done(function(a) {
    		locMarkers.addLayer(locMarker);
        	locMap.setView(latLng, 13, true);
    	});*/
    });
    
    /*$('#locBtn').on('click', function(e) {
    	locSearch();
    });
    
    $('#locSearch').on('keyup', function(e) {
    	if(e.keyCode == 13) {
    		locSearch();
    	}
    });*/
    
    /*$('#rmvLocation').on('click', function(e) {
    	$.when(removeLoc($.url.param('id'))).done(function(a) {
    		locMarkers.clearLayers();
    		locMap.setView([38.8, -99.3], 3);
    		$('#locList').empty();
    	});
    });*/
    
    $(document).on('click', '#assignOrg', function(e) {
    	e.preventDefault();
    	
    	$('#orgWin').dialog('open');
    });
    
    $(document).on('click', '#btnAssignOrg', function(e) {
    	var asset_id = $('#selOrg').attr('data-asset');
    	var org_id = $('#selOrg').val();
    	
    	$.when(assignAssetToOrg(asset_id, org_id)).done(function(a) {
    		$.when(getAssetOrgList(asset_id)).done(function(b) {
    			$('#orgList').html(b);
    		});
    	});
    });
    
    $(document).on('click', '.rmvAstOrg', function(e) {
    	e.preventDefault();
    	
    	var asset_id = $('#assetId').val();
    	var org_id = $(e.currentTarget).attr('data-org');
    	
    	$.when(removeAssetFromOrg(asset_id, org_id)).done(function(a) {
    		$.when(getAssetOrgList(asset_id)).done(function(b) {
    			$('#orgList').html(b);
    		});
    	});
    });
    
    $(document).on('change', '#imgRights', function(e) {
    	var asset_id = $(e.currentTarget).attr('data-asset');
    	var rights_id = $(e.currentTarget).val();
    	
    	$.when(changeImageRights(asset_id, rights_id)).done(function() {
    		$('<div />').attr('class', 'notification').html('Successfully updated asset rights.').prependTo($(e.currentTarget).parent().parent('.fmElement')).delay(1500).fadeOut('slow', function() {
    			$(this).remove();
    		});
    	});
    });
    
    $(document).on('click', '.selectAvailGrp', function(e) {
        e.preventDefault();
        var group_id = $(e.currentTarget).attr('data-id');
        var asset_id = $.url.param('id');
        
        $.when(group.assignAsset(asset_id, group_id)).done(function(a) {
            /**
             * list all groups assigned to this asset
             */
            $("#groupsList").load(globals.ajaxurl + 'assetGroupsList.php', {'id':asset_id});
            
            /**
             * 
             * update available groups for the asset
             */
            $.when(group.getAvailable($.url.param('id'))).done(function(a) {
               $('#availSets').html(a);
            });
        });
    });
});

function changeImageRights(asset_id, rights_id) {
	return $.post(globals.ajaxurl + 'asset.php', {
		_mode:'rights',
		_task:'change',
		'asset_id':asset_id,
		'rights_id':rights_id
	}, null, 'json');
}

function removeAssetFromOrg(asset_id, org_id) {
	return $.post(globals.ajaxurl + 'asset.php', {
		_mode:'org',
		_task:'rmv',
		'asset_id':asset_id,
		'org_id':org_id
	}, null, 'json');
}

function getAssetOrgList(asset_id) {
	return $.post(globals.ajaxurl + 'asset.php', {
		_mode:'org',
		'asset_id':asset_id
	}, null, 'html');
}

function assignAssetToOrg(asset_id, org_id) {
	return $.post(globals.ajaxurl + 'asset.php', {
		_mode:'org',
		_task:'add',
		'asset_id':asset_id,
		'org_id':org_id
	}, null, 'json');
}

function getLoc(asset_id) {
	return $.post(globals.ajaxurl + 'asset.php', {_mode:'loc', 'asset_id':asset_id}, null, 'json');
}

function removeLoc(asset_id) {
	return $.post(globals.ajaxurl + 'asset.php', {_mode:'loc', _task:'rmv', 'asset_id':asset_id}, null, 'json');
}

function assignShoot(shoot_id, asset_id) {
	return $.post(globals.ajaxurl+'shoots.php', {_mode:'asset',_task:'assign',_id:shoot_id,_aid:asset_id}, null, 'json');
}

function addShoot(data) {
	return $.post(globals.ajaxurl + 'shoots.php', data, null, 'json');
}

function removeShoot(asset_id) {
	return $.post(globals.ajaxurl + 'shoots.php', {_mode:'remove', 'asset_id':asset_id}, null, 'json');
}

function updateEditForm(asset_id) {
	return $.post(globals.ajaxurl + 'shoots.php', {_mode:'get', _task:'one', 'asset_id':asset_id}, null, 'html');
}

function doOSMSearch(query) {
	return $.get('http://nominatim.openstreetmap.org/search/', {'q':query, 'format':'json'}, null, 'json');
}

function locSearch() {
	$('#locList').empty();
	/*locMarkers.clearLayers();
	
	if($('#locSearch').val() != '') {
		$.when(doOSMSearch($('#locSearch').val())).done(function(a) {
			$(a).each(function(i, v) {
				$('<div />').attr({'class':'locListItem', 'data-lat':v.lat, 'data-lon':v.lon}).html(v.display_name).appendTo('#locList');
				
				var locLatLng = new L.LatLng(v.lat, v.lon);
				var locMarker = new L.Marker(locLatLng);
				locMarkers.addLayer(locMarker);
				locBounds.extend(locLatLng);
			});
			
			locMap.fitBounds(locBounds);
		});
	}*/
}

/*function updateAssetLoc(asset_id, lat_lng, loc_name) {
	return $.post(globals.ajaxurl + 'asset.php', {
		_mode:'loc',
		_task:'edit',
		'asset_id':asset_id,
		loc:loc_name,
		lat:lat_lng.lat,
		lon:lat_lng.lng
	}, null, 'json');
}*/

function getOrgsString(asset_id) {
	return $.post(globals.ajaxurl + 'asset.php', {
		_mode:'org',
		_task:'str',
		'asset_id':asset_id
	}, null, 'json');
}