/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var _ZDEBUG = false;
var zakaz_controller = {
    uiDTitle: null,
    isInited: false,
    isChanged: false,
    tm: 0,
    busy: 0,
    _sendFreq: 6,
    _resetFrq: false,
    _isOpl: false,
    closeAnyWhay: false,
    tabs: [ ],
    executersList: [ ],
    _productCategory: 0,
    _productCategory2: 0,
    _productCategoryOld: null,
    _openTechnikalsAfterClose: false,
    options: {
        requestWorkTypes: null,
        requestProdaction: null,
        modal: true,
        isCopy: false,
        copyAsReprint: false,
        parent: null,
        minimizable: false,
        tabIndex: 1,
        z_id: null,
        requestUrl: null,
        requestDraftUrl: null,
        validateUrl: null,
        dateFormat: 'dd.mm.yy',
        bigLoaderPicUrl: null,
        customerListUrl: null,
        customerManageDetailUrl: null,
        materialInfoUrl: null,
        canselUrl: null,
        getFileUrl: null,
        toRemoveUrl: null,
        toTempStoreUrl: null,
        toResetMaterialUrl: null,
        publishfileFileUrl: null,
        sendTmpTimeOut: 6,
        storedZClick: null,
        storedZContextMenu: null,
        storedZRemoveAll: null,
        mailFileUrl: null,
        mailGetZkazchikEmails: null,
        tempFileList: null,
        isDisainer: false,
        isDirt: false,
        viewRowUrl: null,
        liffCutterUrl: false,
        width: 1055,
        afterSave: null,
        materials: {
            title: 'Выберите материал',
            bodyBackgrounColor: '#66c2d1',
            formName: "Tablestypes",
            loadPicUrl: null,
            bigLoaderPicUrl: null,
            materialRequestListUrl: null,
            materialTablesListUrl: null,
            suppliersRequestUrl: null,
            subtablegetlistUrl: null,
            subtabladdeditUrl: null,
            removeSubTUrl: null,
            referenceColumnName: null,
            subTableDDListUrl: null,
            picMode: true,
            isAdmin: true,
        },
        smallTableListUrl: null,
        getpostrmanager: null,
        getpodinfo: null,
        restore: null,
        restoreProductCategory: null,
        restoreProductCategory2: null,
        isAdmin: false,
        form: {
            name: '',
            fields: {
            },
            attributeLabels: {}
        }
    },
    doOpenTecnicals: function ( ) {
        return this._openTechnikalsAfterClose;
    },
    __setDefaultBlock: function ( num, isNew ) {
        num = num ? num : 0;
        this.options.form.fields.blocks_type = this.options.form.fields.blocks_type && this.options.form.fields.blocks_type < 10 ? this.options.form.fields.blocks_type : 0;
        //if ( _ZDEBUG ) console.log( Object.keys( this._productCategory2 ), this.options.form.fields.blocks_type, $.inArray( this.options.form.fields.blocks_type.toString( ), Object.keys( this._productCategory2 ) ) );
        if ( $.inArray( this.options.form.fields.blocks_type.toString( ), Object.keys( this._productCategory2 ) ) === -1 ) {
            this.options.form.fields.blocks_type = Object.keys( this._productCategory2 ).length ? Object.keys( this._productCategory2 )[0] : 0;
        }
        let tmpBt=this.options.form.fields['blocks_type' + ( num ? num : '' )];
        //mark 18.12.2020 снял коментарий
        //Снял коммент с условия ниже
        if (typeof(tmpBt)==='undefined'){
            tmpBt=this.options.form.fields['blocks_type'];
        }
        return this._block_table( tmpBt, true, num, isNew );
    },
    _changeBlockModeAll: function ( category, num, isNew ) {
        let blk = null;
        if ( this._productCategory <= 2 && $.type( num ) === 'undefined' ) { //last chnge <=
            return null;
        } else {
            num = num ? num : 0;
            switch ( category ) {
                case 1:
                    blk = this._block_table( 10, false, num, isNew );
                    break;
                case 2:
//                        this.zakaz_params_redraw();
                    blk = this._block_table( 11, false, num, isNew );
                    break;
                default:
                    blk = this.__setDefaultBlock( num, isNew );
                    break;
            }
        }
        return blk;
    },
    _changeBlockMode: function ( ui ) {
        //if ( _ZDEBUG ) console.log( ui );
        if ( this.busy )
            return;
        let category = -1;
        if ( $.type( ui ) === 'object' && $.type( ui.item ) === 'object' && $.type( ui.item.category ) !== 'undefined' && $.type( ui.item.category2 ) !== 'undefined' ) {
            category = ui.item.category;
            this._productCategory2 = ui.item.category2;
        } else if ( $.type( ui ) === 'object' && $.type( ui.category ) !== 'undefined' && $.type( ui.category2 ) !== 'undefined' ) {
            category = ui.category;
            this._productCategory2 = ui.category2;
        } else {
            if ( $( '#Zakaz-production_id' ).attr( 'data-category' ) )
                $( '#Zakaz-production_id' ).removeAttr( 'data-category' );
        }
        if ( category > -1 ) {
            $( '#Zakaz-production_id' ).attr( 'data-category', category );
            this._productCategory = category;
            this._zakaz_material( $( '#tab-pane-zakaz-page2' ).empty( ) );
            let target = $( '.mat-middle-zone' ).children( ).last( ); //.empty();
            this.options.form.fields.materials = [ ];
            if ( this._productCategoryOld === null ) {
                this._productCategoryOld = category;
            } else if ( this._productCategoryOld !== category ) {
                this._productCategoryOld = category;
            }
            this.zakaz_params_redraw( );
            let blk = this._changeBlockModeAll( category );
            target.append( blk );
            this._checkMaterialCount( );
        }
    },
    number_of_copies1_back: null,
    production_id_back: null,
    _prepareTabs: function ( ) {
        let rVal, self = this;
        if ( this.options.isDisainer )
            rVal = [ ];
        else
            rVal = [
                {
                    label: 'Общая',
                    rows: [
                        {
                            fields: [
                                {name: 'production_id', activeDD: {otherParam: {tableName: 'production'}, strictly: true, onClickOrChange: function ( data, ui, dd ) {
                                            $( '#info-Zakaz-production_id, #zakaz_material_prod_info, #zakaz_pprint_prod_info, #zakaz_printcut_prod_info, #zakaz_executers_prod_info' ).val( $( this ).val( ) );
                                            //if ( _ZDEBUG ) console.log( '_changeBlockMode', ui, this );
                                            let m_count = $( '.mat-middle-zone' ).children( 'div:first-child' ).children( ).length - ( ( self._productCategory > 1 && self._productCategory != 3 ) ? 1 : 1 );
                                            if ( !ui )
                                                return;
                                            if ( self.production_id_back === null
                                                    || self.production_id_back.label !== ui.item.label
                                                    || self.production_id_back.value !== ui.item.value
                                                    || self.production_id_back.category !== ui.item.category ) {
                                                if ( m_count > 0 && self.production_id_back !== null ) {
                                                    let el = this;
                                                    m_alert( 'Внимание', 'При изменение типа продукции<br>материалы будут потеряны.', {
                                                        label: 'Продолжить',
                                                        click: function ( ) {
                                                            self._changeBlockMode( ui );
                                                            self.production_id_back = ui.item;
                                                            let tmpBlock = {block: {}};
                                                            self._blockProceed( 0, tmpBlock );
                                                            self._blockProceed( 1, tmpBlock );
                                                            $( '#Zakaz-format_printing_block,#Zakaz-blocks_per_sheet,#Zakaz-num_of_products_in_block,#Zakaz-num_of_products_in_block1,#Zakaz-num_of_printing_block' ).val( '' );
                                                            with ( self.options.form.fields ) {
                                                                //blocks_type
                                                                if ( typeof blocks_type !== 'undefined' && blocks_type )
                                                                    blocks_type = 1;
                                                                if ( typeof blocks_type1 !== 'undefined' && blocks_type1 )
                                                                    blocks_type1 = 1;
                                                                if ( typeof format_printing_block !== 'undefined' && format_printing_block )
                                                                    format_printing_block = '';
                                                                if ( typeof blocks_per_sheet !== 'undefined' && blocks_per_sheet )
                                                                    blocks_per_sheet = '';
                                                                if ( typeof num_of_products_in_block !== 'undefined' && num_of_products_in_block )
                                                                    num_of_products_in_block = '';
                                                                if ( typeof num_of_printing_block !== 'undefined' && num_of_printing_block )
                                                                    num_of_printing_block = '';
                                                                if ( typeof format_printing_block1 !== 'undefined' && format_printing_block1 )
                                                                    format_printing_block1 = '';
                                                                if ( typeof blocks_per_sheet1 !== 'undefined' && blocks_per_sheet1 )
                                                                    blocks_per_sheet1 = '';
                                                                if ( typeof num_of_products_in_block1 !== 'undefined' && num_of_products_in_block1 )
                                                                    num_of_products_in_block1 = '';
                                                                if ( typeof num_of_printing_block1 !== 'undefined' && num_of_printing_block1 )
                                                                    num_of_printing_block1 = '';
                                                            }
                                                            $( '#Zakaz-product_size' ).val( '' );
                                                        }
                                                    }, {
                                                        label: 'Отменить',
                                                        click: function ( ) {
                                                            dd.setOldValue( self.production_id_back.value );
                                                            el.val( self.production_id_back.label );
                                                            el.parent( ).prev( ).val( self.production_id_back.value ).attr( {
                                                                'data-category': self.production_id_back.category
                                                            } );
                                                        }
                                                    } );
                                                } else {
                                                    self._changeBlockMode( ui );
                                                    self.production_id_back = ui.item;
                                                }
                                            }
                                        }, onReady: function ( ui ) {
                                            $( '#info-Zakaz-production_id, #zakaz_material_prod_info, #zakaz_pprint_prod_info, #zakaz_printcut_prod_info, #zakaz_executers_prod_info' ).val( $( this ).val( ) );
                                            self._changeBlockMode( ui );
                                            self.production_id_back = ui;
                                            //if ( _ZDEBUG ) console.log( '_changeBlockMode', ui, this, this.parent( ).prev( ) );
                                        }}, activeDDUrlVarName: 'smallTableListUrl', createHiddenInput: true, wantAdd: !this.options.isDisainer && this.options.requestProdaction ? function ( input ) {
                                        this.tablesControllerDialog( $.extend( {}, self.options.requestProdaction, {
                                            picMode: true,
                                            autoClose: true,
                                            showDefaultButton: true,
                                            beforeClose: function ( ) {
                                                input.activeDD( "flashcache" );
                                                input.activeDD( "update" );
                                            }
                                        } ) );
                                    } : null},
                                {name: 'name', controlSize: 220},
                                {name: 'number_of_copies', defValue: 0, addGoupClass: 'small-gr', onChange: function ( ) {
                                        $( '#info-Zakaz-number_of_copies, #zakaz_material_copies_info, #zakaz_pprint_copies_info, #zakaz_printcut_copies_info, #zakaz_executers_copies_info' ).val( $( this ).val( ) );
                                        //if ( _ZDEBUG ) console.log( self._productCategory );
                                        //uflak-tirage
                                        if ( self._productCategory === 3 && $( '#uflak-tirage' ).length ) {
                                            if ( $( this ).val( ) )
                                                $( '#uflak-tirage' ).val( parseInt( $( this ).val( ) ) + 100 );
                                            else
                                                $( '#uflak-tirage' ).val( 0 );
                                        }
                                        if ( !self.busy && self._productCategory != 1 && self._productCategory != 2 ) {
                                            //if ( _ZDEBUG ) console.log( 'chkNew', self );
                                            let m_count = $( '.mat-middle-zone' ).children( 'div:first-child' ).children( ).length - ( ( this._productCategory > 1 && this._productCategory != 3 ) ? 1 : 1 );
                                            if ( m_count > 0 ) {
                                                if ( $( '#blockRecalButton' ).length ) {
                                                    $( '[href="#tab-pane-zakaz-page2"]' ).trigger( 'click' );
                                                    $( '#blockRecalButton' ).trigger( 'click' );
                                                } else
                                                    m_alert( 'Внимание', 'Проверте количество материала на вкладке натериал', {
                                                        label: 'Перейти',
                                                        click: function ( ) {
                                                            //if ( _ZDEBUG ) console.log( $( '[href="#tab-pane-zakaz-page2"]' ) );
                                                            $( '[href="#tab-pane-zakaz-page2"]' ).trigger( 'click' );
                                                        }
                                                    }, 'Закрыть' );
                                            }
                                        }
                                        if ( self._productCategory == 1 || self._productCategory == 2 ) {
                                            if ( $.isFunction( self.__block10Recalc ) ) {
                                                self.__block10Recalc( {data: {self: self}} );
                                            } else if ( $.isFunction( self.__block11Recalc ) ) {
                                                self.__block11Recalc( {data: {self: self}} );
                                            }
                                        }
                                    }},
                                {name: 'number_of_copies1', defValue: 0, addGoupClass: 'small-gr', onFocus: function ( e ) {
                                        self.number_of_copies1_back = $( this ).val( );
                                        if ( $.type( self.number_of_copies1_back ) === 'undefined' )
                                            self.number_of_copies1_back = 0;
                                    }, onChange: function ( ) {
                                        $( '#info-Zakaz-number_of_copies1, #zakaz_material_copies_info1, #zakaz_pprint_copies_info1, #zakaz_printcut_copies_info1, #zakaz_executers_copies_info1' ).val( $( this ).val( ) );
                                        let m_count = $( '.mat-middle-zone' ).children( 'div:first-child' ).children( ).length - ( ( self._productCategory > 1 && self._productCategory != 3 ) ? 1 : 1 );
                                        let val = $( this ).val( ), el = this;
                                        let isCanseled = false;
                                        val = val.length ? val : 0;
                                        //if ( _ZDEBUG ) console.log( 'm_count', m_count );
                                        if ( ( !val || val == 0 ) && m_count > 1 ) {
                                            m_alert( 'Внимание', 'Второй материал будет удалён!', 'Продолжить', {
                                                label: 'Отменить',
                                                click: function ( ) {
                                                    isCanseled = true;
                                                }
                                            }, function ( ) {
                                                //if ( _ZDEBUG ) console.log( 'is' + ( isCanseled ? 'Canseled' : 'Ok' ) );
                                                if ( isCanseled ) {
                                                    $( el ).val( self.number_of_copies1_back );
                                                } else {
                                                    $( '#Remove-material-Button' ).attr( 'data-notask', true ).trigger( 'click' );
//                                                    if ( self._productCategory > 1 && self._productCategory != 3 ) {
//                                                        $( '.mat-middle-zone' ).children( 'div:first-child' ).children( ':nth-child(' + ( m_count + 1 ) + ')' ).remove();
//                                                    } else {
//                                                        $( '.mat-middle-zone' ).children( 'div:first-child' ).children( ':nth-child(' + ( m_count ) + ')' ).remove();
//                                                    }
                                                    self._checkMaterialCount( );
                                                }
                                            } );
                                        } else {
                                            self._checkMaterialCount( );
                                            if ( self._productCategory == 1 || self._productCategory == 2 ) {
                                                if ( $.isFunction( self.__block10Recalc ) ) {
                                                    self.__block10Recalc( {data: {self: self}} );
                                                } else if ( $.isFunction( self.__block11Recalc ) ) {
                                                    self.__block11Recalc( {data: {self: self}} );
                                                }
                                            }
                                            if ( !self.busy && self._productCategory != 1 && self._productCategory != 2 ) {
                                                let m_count = $( '.mat-middle-zone' ).children( 'div:first-child' ).children( ).length - ( ( this._productCategory > 1 && this._productCategory != 3 ) ? 1 : 1 );
                                                if ( m_count > 1 ) {
                                                    if ( $( '#blockRecalButton1' ).length ) {
                                                        $( '[href="#tab-pane-zakaz-page2"]' ).trigger( 'click' );
                                                        $( '#blockRecalButton1' ).trigger( 'click' );
                                                    } else
                                                        new m_alert( 'Внимание', 'Проверте количество материала на вкладке натериал', {
                                                            label: 'Перейти',
                                                            click: function ( ) {
                                                                //if ( _ZDEBUG ) console.log( $( '[href="#tab-pane-zakaz-page2"]' ) );
                                                                $( '[href="#tab-pane-zakaz-page2"]' ).trigger( 'click' );
                                                            }
                                                        }, 'Закрыть' );
                                                }
                                            }
                                        }
                                    }}
                            ]
                        }, {
                            fields: [
                                {name: 'worktypes_id', activeDD: {otherParam: {tableName: 'worktypes', category: [ 0, 1, 2 ]}, strictly: true}, activeDDUrlVarName: 'smallTableListUrl', createHiddenInput: true, wantAdd: !this.options.isDisainer && this.options.requestWorkTypes ? function ( input ) {
                                        this.tablesControllerDialog( $.extend( {}, self.options.requestWorkTypes, {
                                            picMode: true,
                                            autoClose: true,
                                            showDefaultButton: true,
                                            beforeClose: function ( ) {
                                                input.activeDD( "flashcache" );
                                                input.activeDD( "update" );
                                            }
                                        } ) );
                                    } : null},
                                {name: 'stage', activeDD: {otherParam: {tableName: 'stage'}, strictly: true}, activeDDUrlVarName: 'smallTableListUrl', createHiddenInput: true},
                                {name: 'division_of_work', activeDD: {otherParam: {tableName: 'division_of_work'}, strictly: true}, activeDDUrlVarName: 'smallTableListUrl', createHiddenInput: true}
                            ]
                        }, {
                            fields: [
                                {name: 'product_size', controlSize: 220, addOn: 'мм.', onChange: function ( ) {
                                        if ( !self.busy ) {
                                            if ( $( '[name="Zakaz[materials][0][type_id]"]' ).length ) {
                                                m_alert( 'Внимание', '<p>Вы изменили размер изделия!</p><h3>Проверти параметры блока!</h3><p>Перейти на вкладку "Материал"</p>', function ( ) {
                                                    $( 'a[href="#tab-pane-zakaz-page2"]' ).trigger( 'click' );
                                                    let i = 0, tm;
                                                    tm = setInterval( function ( ) {
                                                        i++;
                                                        if ( i % 2 === 0 ) {
                                                            $( '#block-body' ).css( 'color', '#F00' );
                                                        } else {
                                                            $( '#block-body' ).removeAttr( 'style' );
                                                        }
                                                        if ( i === 40 ) {
                                                            clearInterval( tm );
                                                            $( '#block-body' ).removeAttr( 'style' );
                                                        }
                                                    }, 40 );
                                                    if ( $( '#blockRecalButton0' ).length ) {
                                                        $( '#blockRecalButton0' ).trigger( 'click', {afterEnd: function ( ) {
                                                                if ( $( '#blockRecalButton1' ).length ) {
                                                                    $( '#blockRecalButton1' ).trigger( 'click' );
                                                                }
                                                            }} );
                                                    } else if ( $( '#blockRecalButton1' ).length ) {
                                                        $( '#blockRecalButton1' ).trigger( 'click' );
                                                    }
                                                } );
                                            }
                                        }
                                    }},
                                '_isExpress',
                                '_rePrint'
                            ]
                        }, {
                            fields: [
                                {name: 'attention', controlSize: false, addGoupClass: 'attention', controlTag: 'textarea'},
                            ]
                        }, {
                            labelClass: 'dialog-col-160',
                            fields: [
                                {name: 'method_of_payment', controlSize: 95, activeDD: {otherParam: {tableName: 'method_of_payment'}, strictly: true}, activeDDUrlVarName: 'smallTableListUrl', addGoupClass: 'pull-left', inline: true, createHiddenInput: true},
                                {name: 'account_number', addGoupClass: 'pull-left', inline: true, controlSize: 140},
                            ]
                        }, {
                            labelClass: 'dialog-col-160',
                            fields: [
                                {name: 'invoice_from_this_company', controlSize: 95, activeDD: {otherParam: {tableName: 'invoice_from_this_company'}, strictly: true}, activeDDUrlVarName: 'smallTableListUrl', addGoupClass: 'pull-left', inline: true, createHiddenInput: true},
                                {name: 'zakUrFace', controlSize: 185, activeDD: {afterUpdate: function ( ) {
                                            let val = self.options.form.fields.zakUrFace ? self.options.form.fields.zakUrFace : 0;
                                            let txt = $( this ).activeDD( 'findByValue', val );
                                            if ( txt ) {
                                                $( this ).val( txt.label );
                                                $( '#Zakaz-zakUrFace' ).val( val );
                                                $( this ).attr( 'data-key', val );
                                            } else {
                                                val = 0;
                                                txt = $( this ).activeDD( 'findByValue', val );
                                                if ( txt ) {
                                                    $( this ).val( txt.label );
                                                    $( '#Zakaz-zakUrFace' ).val( val );
                                                    $( this ).attr( 'data-key', val );
                                                }
                                            }
                                        }, otherParam: {tableName: 'zakUrFace_list', zakazchikId: 0}, cacheable:false, autoLoad: false, strictly: true}, activeDDUrlVarName: 'smallTableListUrl', addGoupClass: 'pull-left', inline: true, createHiddenInput: true},
                            ]
                        },
                    ]
                },
                {
                    label: 'Исполнитель',
                    rows: '_executers'
                },
                {
                    label: 'Материал',
                    rows: '_zakaz_material'
                },
                {
                    label: 'Печать+резка',
                    rows: 'zakaz_params_row',
                },
                {
                    label: 'Постпечать',
                    rows: '_postprint_rows'
                }
            ];
        rVal[rVal.length] =
                {
                    label: 'Файлы',
                    rows: 'zakaz_file'
                };
        if ( this.options.isDisainer )
            rVal[rVal.length] =
                    {
                        label: 'Техничка',
                        rows: '_Tech_tab'
                    };
        rVal[rVal.length] = {
            label: 'Коментарии',
            rows: [
                {
                    fields: [
                        {name: 'design_comment', controlSize: false, addGoupClass: 'comments-area', controlTag: 'textarea', readonly: !this.options.isAdmin && this.options.isProizvodstvo},
                    ]
                },
                {
                    fields: [
                        {name: 'proizv_comment', controlSize: false, addGoupClass: 'comments-area', controlTag: 'textarea', readonly: !this.options.isAdmin && !this.options.isProizvodstvo},
                    ]
                }
            ]
        }
        if ( this.options.isAdmin ) {
            rVal[rVal.length] = {
                label: 'Менеджер',
                rows: [
                    {
                        labelClass: 'dialog-col-160',
                        fields: [
                            {name: 'ourmanagername', addGoupClass: 'pull-left', inline: true, readonly: true, controlSize: 157},
                            {name: 'wages', addGoupClass: 'pull-left', readonly: true, inline: true, controlSize: 157},
                        ]
                    },
                    {
                        labelClass: 'dialog-col-160',
                        fields: [
                            {name: 'percent1', addGoupClass: 'pull-left', readonly: true, inline: true, controlSize: 157},
                            {name: 'percent2', addGoupClass: 'pull-left', readonly: true, inline: true, controlSize: 157},
                        ]
                    },
                    {
                        labelClass: 'dialog-col-160',
                        fields: [
                            {name: 'percent3', addGoupClass: 'pull-left', readonly: true, inline: true, controlSize: 157},
                        ]
                    }
                ]
            }
        }

        return rVal;
    },
    _create: function ( ) {
        let self = this;
        //if (if (this.options.isDisainer))
        this.options.buttons = [ ];
        if ( !this.options.isDisainer ) {
            this.options.buttons.push( {
                text: "Техничка",
                click: function ( ) {
                    self._saveClick.call( self, this, false, function ( ) {
                        self._openTechnikalsAfterClose = true;
                    } );
                },
                role: 'save-tech-button'
            } );
        }
        this.options.buttons.push( {
            text: "Сохранить",
            click: function ( ) {
                self._saveClick.call( self, this );
            },
            role: 'save-button'
        } );
        this.options.buttons.push( {
            text: "Черновик",
            click: function ( ) {
                self._saveClick.call( self, this, true );
            }
        } );
        this.options.buttons.push( {
            text: "Отмена",
            click: function ( ) {
                self._cansel.call( self, this );
            }
        } );
        if ( this.options.isDisainer ) {
            this.options.width = 1200;
        }
        this._super( );
        //$( "button.ui-dialog-titlebar-close" ).hide(); Спрятать кнопку крестик
        this._useBeforeCloseEvent = false;
        this.tabs = this._prepareTabs( );
        this._sendFreq = this.options.sendTmpTimeOut;
        this.options.beforeClose = this._beforeClose;
        this.options.materials.bigLoaderPicUrl = this.options.materials.bigLoaderPicUrl ? this.options.materials.bigLoaderPicUrl : this.options.bigLoaderPicUrl
        this.startTabIndex = this.options.tabIndex;
//            //if ( _ZDEBUG ) console.log(this);
        this.uiDialog.addClass( 'zakaz-popup dialog-list' );
        this.uiDTitle = this.uiDialogTitlebarClose.prev( );
        this.uiDTitle.addClass( 'dialog-control-group-inline' );
        //$.ui.dialog.prototype._create.call(this);
        //if ( _ZDEBUG ) console.log( this );
        if ( this.options.isDisainer || this.options.isDirt )
            $( this.uiButtonSet.children( ).get( 1 ) ).remove( );
    },
    open: function ( ) {
        this._super( );
		window.ddId=10000;
        this.element.empty( );
        this._dorequest( true );
    },
    _clearTempFilesAndClose: function ( ) {
        if ( this.options.canselUrl ) {
            this._hideBunner( );
            this._showBunner( 'Очистка временных файлов' );
            let self = this;
            if ( this.options.form.fields.tmpName ) {
                $.post( this.options.canselUrl, {tmpName: this.options.form.fields.tmpName} ).done( function ( data ) {
                    //if ( _ZDEBUG ) console.log( 'closeAnyWhay:cleaneCoplete', data );
                    self.replaceMainMenu( data.menu, data.label );
                    self.close( );
                } ).fail( function ( er1 ) {
                    if ( $.type( er1.responseJSON ) === 'object' )
                        m_alert( er1.responseJSON.name, er1.responseJSON.message, true, false );
                    else
                        m_alert( "Ошибка сервера", er1.responseText, true, false );
                } );
                ;
            } else {
                this.close( );
            }
        } else
            this.close( );
    },
    closeAnyWhay: function ( ) {
        this.options.beforeClose = null;
        if ( this._sendInterval ) {
            clearInterval( this._sendInterval );
        }
        if ( this._jqXHRCount ) {
            this._showBunner( 'Отмена' );
            this._fileAbort = function ( ) {
                this._clearTempFilesAndClose( );
            }
        } else {
            this._clearTempFilesAndClose( );
        }
    },
    _cansel: function ( ) {
        this.close( );
    },
    _beforeClose: function ( event, ui ) {
        //if ( _ZDEBUG ) console.log( 'beforeClose', this );
        let el = $( this );
        new mDialog.m_alert( {
            content: 'Новые данные <b style="color:red">НЕ СОХРАНЯТСЯ</b>!',
            okClick: {
                label: 'заказ ЗАКРЫТЬ',
                click: function ( ) {
                    el.zakazAddEditController( 'closeAnyWhay' );
                },
                class: 'btn btn-red'
            },
            canselClick: {
                label: 'ПРОДОЛЖИТЬ редактирование',
                class: 'btn btn-gray-text-black'
            },
            showCloseButton: false
        } );
        return false;
    },
    _checkPontonErrors: function ( ) {
        let hasError = false;
        let errorElement = null;
        let self = this;
        $( '#tab-pane-zakaz-page3' ).find( '[name*="[face_values]"],[name*="[back_values]"],[name*="[side_values]"],[name*="[clips_values]"]' ).each( function ( ) {
            hasError = !$( this ).val( ).length && !$( this ).attr( 'disabled' ) && self._productCategory != 3;
            if ( hasError )
                errorElement = this;
            return !hasError;
        } );
        if ( hasError ) {
            this._hideBunner( );
            m_alert( 'Ошибка заполнения', 'На вкладке "Печать+резка" имеются незаполненные поля!', function ( ) {
                $( '[href="#tab-pane-zakaz-page3"]' ).trigger( 'click' );
                $( errorElement ).trigger( 'focus' );
            }, false );
            return false;
        }
        return true;
    },
    _checkPodryadErrors: function ( ) {
        let re_print = $( '#Zakaz-re_print' ).length && $( '#Zakaz-re_print' ).val( ).length;
        let errorText = '';
        let hasError = false;
        let errorElement = null;
        //if ( _ZDEBUG ) console.log( '_checkPodryadErrors' );
        $( '#executers_table' ).children( 'tbody' ).children( '[data-key]' ).each( function ( ) {
            let v1 = $( this ).children( ':nth-child(4)' ).children( 'div:first-child' ).children( ':first-child' );
            let v2 = $( this ).children( ':nth-child(5)' ).children( 'div:first-child' ).children( ':first-child' );
            //if ( _ZDEBUG ) console.log( '_checkPodryadErrors', v1.val( ).length, v1.val( ) );
            hasError = !re_print && ( !v1.val( ).length || v1.val( ) == 0 );
            //v2=v1.length && v1 != 0;
            if ( hasError ) {
                errorElement = v1;
                errorText = 'На вкладке "Исполнитель" не указана стоимость "' + $( this ).children( ':nth-child(3)' ).children( 'div:first-child' ).children( ':nth-child(2)' ).val( ) + '"!';
            }
            return !hasError;
        } );
        if ( hasError ) {
            this._hideBunner( );
            m_alert( 'Ошибка заполнения', errorText, function ( ) {
                $( '[href="#tab-pane-zakaz-page1"]' ).trigger( 'click' );
                errorElement.trigger( 'focus' );
            }, false );
            return false;
        }

        return this._checkPontonErrors( );
    },
    _checkRaspakovkaUpakovkaError: function ( ) {
        let isSuvenir = false;
        if ( $.type( $( '#Zakaz-production_id' ).attr( 'data-category' ) ) !== 'undefined' )
            isSuvenir = $( '#Zakaz-production_id' ).attr( 'data-category' ) === '2';
        else
            isSuvenir = this._productCategory === 2;
        if ( isSuvenir || this._productCategory === 1 ) {
            let hasError = false;
            if ( $( '#uf-lak' ).val( ) === '1' ) {
                let chk = false;
                $( '#executers_table' ).children( 'tbody' ).children( '[data-key]' ).each( function ( ) {
                    let el = $( this ).children( ':nth-child(3)' ).children( 'div:first-child' ).children( ':nth-child(2)' );
                    //if ( _ZDEBUG ) console.log( el.val( ) );
                    chk = el.val( ) === 'Распаковка';
                    return !chk;
                } );
                hasError = !chk;
            }
            if ( hasError ) {
                this._hideBunner( );
                m_alert( 'Ошибка заполнения', 'На вкладке "Печать+резка" указано, что требуется "Распаковка", а она не указана на вкладке "Исполнитель"!', function ( ) {
                    $( '[href="#tab-pane-zakaz-page1"]' ).trigger( 'click' );
                }, false );
                return false;
            }
            if ( $( '#thermal-lift' ).val( ) === '1' ) {
                let chk = false;
                $( '#executers_table' ).children( 'tbody' ).children( '[data-key]' ).each( function ( ) {
                    let el = $( this ).children( ':nth-child(3)' ).children( 'div:first-child' ).children( ':nth-child(2)' );
                    //if ( _ZDEBUG ) console.log( el.val( ) );
                    chk = el.val( ) === 'Упаковка';
                    return !chk;
                } );
                hasError = !chk;
            }
            if ( hasError ) {
                this._hideBunner( );
                m_alert( 'Ошибка заполнения', 'На вкладке "Печать+резка" указано, что требуется "Упаковка", а она не указана на вкладке "Исполнитель"!', function ( ) {
                    $( '[href="#tab-pane-zakaz-page1"]' ).trigger( 'click' );
                }, false );
                return false;
            }
            let chk = 0;
            $( '#executers_table' ).children( 'tbody' ).children( '[data-key]' ).each( function ( ) {
                let el = $( this ).children( ':nth-child(3)' ).children( 'div:first-child' ).children( ':nth-child(2)' );
                //if ( _ZDEBUG ) console.log( el.val( ) );
                if ( el.val( ) === 'Упаковка' && $( '#thermal-lift' ).val( ) !== '1' ) {
                    chk = 1;
                } else if ( el.val( ) === 'Распаковка' && $( '#uf-lak' ).val( ) !== '1' ) {
                    chk = 2;
                }
                return !chk;
            } );
            if ( chk ) {
                this._hideBunner( );
                m_alert( 'Ошибка заполнения', 'На вкладке "Исполнитель" указана работа - "' + ( chk === 1 ? 'Упаковка' : 'Распаковка' ) + '", а она не отмечена на вкладке "Печать+резка"!', function ( ) {
                    $( '[href="#tab-pane-zakaz-page3"]' ).trigger( 'click' );
                }, false );
                return false;
            }
        }
        return this._checkPodryadErrors( );
    },
    _checkOtherError: function ( ) {
        let otherError = false, errorElement = null, self = this;
        let counMessage = 'На вкладке "Материал" не указано количество материала';
        let counActionYes = function ( ) {
            $( '[href="#tab-pane-zakaz-page2"]' ).trigger( 'click' );
            $( errorElement ).trigger( 'focus' );
        };
        let counActionNo = false;
        let targetEl = [ ];
        let m_count = $( '.mat-middle-zone' ).children( 'div:first-child' ).children( ).length - ( ( this._productCategory > 1 && self._productCategory != 3 ) ? 1 : 1 );
        //if ( _ZDEBUG ) console.log( $( '#tab-pane-zakaz-page2' ).find( '.mat-middle-zone' ).find( '[name*=count]' ) );
        let tmp = 0, tmp2 = 0;
        $( '#tab-pane-zakaz-page2' ).find( '.mat-middle-zone' ).find( '[name*=count]' ).each( function ( key ) {
            //if ( _ZDEBUG ) console.log( 'check', this, $( this ).val( ) );
            if ( self._productCategory == 3 ) {
                if ( $( '#bf_with_a_stock' + ( key != 0 ? key : '' ) ).length ) {
                    tmp = parseInt( $( '#bf_with_a_stock' + ( key != 0 ? key : '' ) ).text( ) );
                    tmp2 = parseInt( $( this ).val( ) );
                    tmp = !isNaN( tmp ) ? tmp : 0;
                    tmp2 = !isNaN( tmp2 ) ? tmp2 : 0;
                    counMessage = 'По расчёту требуется ' + tmp + ' ' + $.fn.endingNums( tmp, [ 'лист', 'листа', 'листов' ] );
                } else {
                    tmp = self._totalCount( key != 0 ? key : '' );
                    tmp2 = parseInt( $( this ).val( ) );
                    tmp = !isNaN( tmp ) ? tmp : 0;
                    tmp2 = !isNaN( tmp2 ) ? tmp2 : 0;
                    counMessage = 'По расчёту требуется ' + tmp + ' ' + $.fn.endingNums( tmp, [ 'лист', 'листа', 'листов' ] );
                }
            } else if ( ( self._productCategory == 0 ) && $( '#bf_with_a_stock' + ( key != 0 ? key : '' ) ).length ) {
                //if ( _ZDEBUG ) console.log( '#bf_with_a_stock' + ( key != 0 ? key : '' ), $( '#bf_with_a_stock' + ( key != 0 ? key : '' ) ), $( '#bf_with_a_stock' + ( key != 0 ? key : '' ) ).val( ) );
                tmp = parseInt( $( '#bf_with_a_stock' + ( key != 0 ? key : '' ) ).text( ) );
                tmp2 = parseInt( $( this ).val( ) );
                tmp = !isNaN( tmp ) ? tmp : 0;
                tmp2 = !isNaN( tmp2 ) ? tmp2 : 0;
                counMessage = 'По расчёту требуется ' + tmp + ' ' + $.fn.endingNums( tmp, [ 'лист', 'листа', 'листов' ] );
            } else if ( self._productCategory === 1 && $( '#bf_with_a_stock' + ( key != 0 ? key : '' ).length ) ) {
                //if ( _ZDEBUG ) console.log( '#Zakaz-num_of_printing_block' + ( key != 0 ? key : '' ), $( '#Zakaz-num_of_printing_block' + ( key != 0 ? key : '' ) ), $( '#Zakaz-num_of_printing_block' + ( key != 0 ? key : '' ) ).val( ) );
                tmp = parseInt( $( '#Zakaz-num_of_printing_block' + ( key != 0 ? key : '' ) ).val( ) );
                tmp2 = parseInt( $( this ).val( ) );
                tmp = !isNaN( tmp ) ? tmp : 0;
                tmp2 = !isNaN( tmp2 ) ? tmp2 : 0;
                counMessage = 'По расчёту требуется ' + tmp + ' ' + $.fn.endingNums( tmp, [ 'пакет', 'пакета', 'пакетов' ] );
            } else if ( self._productCategory === 2 && $( '#bf_with_a_stock' + ( key != 0 ? key : '' ).length ) ) {
                //if ( _ZDEBUG ) console.log( '#Zakaz-num_of_printing_block' + ( key != 0 ? key : '' ), $( '#Zakaz-num_of_printing_block' + ( key != 0 ? key : '' ) ), $( '#Zakaz-num_of_printing_block' + ( key != 0 ? key : '' ) ).val( ) );
                tmp = parseInt( $( '#Zakaz-num_of_printing_block' + ( key != 0 ? key : '' ) ).val( ) );
                tmp2 = parseInt( $( this ).val( ) );
                tmp = !isNaN( tmp ) ? tmp : 0;
                tmp2 = !isNaN( tmp2 ) ? tmp2 : 0;
                counMessage = 'По расчёту требуется ' + tmp + ' ' + $.fn.endingNums( tmp, [ 'штука', 'штуки', 'штук' ] );
            }

            if ( tmp != tmp2 ) {
                otherError = true;
                errorElement = this;
                counActionYes = {
                    label: 'Авто',
                    click: function ( ) {
                        $( errorElement ).val( tmp ).trigger( 'change' );
                        self.__recalculateAll( true );
                        $( '[role="save-button"]' ).trigger( 'click' );
                    }
                };
                counActionNo = {
                    label: 'Редактировать',
                    click: function ( ) {
                        $( '[href="#tab-pane-zakaz-page2"]' ).trigger( 'click' );
                        $( errorElement ).trigger( 'focus' );
                    }
                };
            }
//                }

            return !otherError;
        } );
        if ( otherError ) {
            this._hideBunner( );
            m_alert( 'Ошибка заполнения', 'Указано ' + ( tmp > tmp2 ? 'недостаточное' : 'избыточное' ) + ' количество материала.<br>' + counMessage, counActionYes, counActionNo );
            return false;
        }
        $( '#tab-pane-zakaz-page2' ).find( '.mat-middle-zone' ).find( '[name*=coast]' ).each( function ( ) {
            if ( !$( this ).val( ) || $( this ).val( ) == 0 ) {
                let radio = $( this ).parent( ).parent( ).prev( ).children( ':first-child' );
                //if ( _ZDEBUG ) console.log( radio );
                if ( radio.radioButton( 'value' ) == 2 ) {
                    otherError = true;
                    errorElement = this;
                }
            }
            return !otherError;
        } );
        if ( otherError ) {
            this._hideBunner( );
            m_alert( 'Ошибка заполнения', 'На вкладке "Материал" не указана стоимость материала', function ( ) {
                $( '[href="#tab-pane-zakaz-page2"]' ).trigger( 'click' );
                $( errorElement ).trigger( 'focus' );
            }, false );
            return false;
        }
        //if ( _ZDEBUG ) console.log( $( '#Zakaz-re_print' ).lenght, $( '#Zakaz-re_print' ).val( ) );
        //Найти наш материал:
        if ( $( '[name*="[supplierType]"]' ).length ) {
            let isFound = false;
            $( '[name*="[supplierType]"]' ).each( function ( ) {
                if ( $( this ).val( ) == 2 )
                    isFound = true;
            } );
            if ( !( $( '#Zakaz-re_print' ).length && $( '#Zakaz-re_print' ).val( ).length )
                    && $( '#Zakaz-profit_type' ).val( ) == 0
                    && ( !$( '#zakaz_material_coast_comerc' ).val( ).length || $( '#zakaz_material_coast_comerc' ).val( ) == 0 )
                    && isFound ) {
                otherError = true;
                this._hideBunner( );
                m_alert( 'Ошибка заполнения', 'На вкладке "Материал" не указана стоимость продажи материала', function ( ) {
                    $( '[href="#tab-pane-zakaz-page2"]' ).trigger( 'click' );
                    $( '#zakaz_material_coast_comerc' ).trigger( 'focus' );
                }, false );
                return false;
            } else if (
                    $( '#Zakaz-profit_type' ).val( ) == 0
                    && ( $( '#zakaz_material_coast_comerc' ).val( ).length && $( '#zakaz_material_coast_comerc' ).val( ) != 0 )
                    && !isFound ) {
                otherError = true;
                this._hideBunner( );
                m_alert( 'Ошибка заполнения', 'На вкладке "Материал" указана стоимость продажи материала.<br>А нашего материала нет!', function ( ) {
                    $( '[href="#tab-pane-zakaz-page2"]' ).trigger( 'click' );
                    $( '#zakaz_material_coast_comerc' ).trigger( 'focus' );
                }, false );
                return false;
            }
        }
        //m_count
        if ( this._productCategory !== 4 && $( '#Zakaz-number_of_copies' ).val( ).length && $( '#Zakaz-number_of_copies' ).val( ) != 0 && m_count < 1 ) {
            this._hideBunner( );
            otherError = true;
            m_alert( 'Ошибка заполнения', 'На вкладке "Материал" не выбрано не одного материала', function ( ) {
                $( '[href="#tab-pane-zakaz-page2"]' ).trigger( 'click' );
                $( '#zakaz-select-material' ).trigger( 'click' );
            }, false );
            return false;
        } else if ( $( '#Zakaz-number_of_copies1' ).val( ).length && $( '#Zakaz-number_of_copies1' ).val( ) != 0 && m_count < 2 ) {
            this._hideBunner( );
            otherError = true;
            m_alert( 'Ошибка заполнения', 'На вкладке "Материал" не выбран второй материал,<br>а указан второй тираж!', function ( ) {
                $( '[href="#tab-pane-zakaz-page2"]' ).trigger( 'click' );
                $( '#zakaz-select-material' ).trigger( 'click' );
            }, false );
            return false;
        }
        counMessage = '';
        let blockCheckEl=[
            {id:'#Zakaz-blocks_per_sheet',msg:'Неправильно заполнено поле "Блоков с листа"'},
            {id:'#Zakaz-format_printing_block',msg:'Неправильно заполнено поле "Формат блока"'},
            {id:'#Zakaz-num_of_products_in_block',msg:'Неправильно заполнено поле "Шт. в блоке"'},
            {id:'#Zakaz-num_of_printing_block',msg:'Неправильно заполнено поле "Кол-во блоков"'},
        ];
        targetEl = [ ];
        for (let i in blockCheckEl){
            let nxt=0
            while(nxt<2){
                let cId=blockCheckEl[i].id+(nxt?nxt:'');
                if ( $( cId ).length && ( !$( cId ).val( ) || $( cId ).val( ) === '0' ) ) {
                    otherError = true;
                    counMessage += blockCheckEl[i].msg+'<br>';
                    targetEl.push( $( cId ) );
                }else if($( cId ).length){
                    $( cId ).removeClass('has-error');
                }
                nxt++;
            }
        }
        if ( otherError ) {
            m_alert( 'Ошибка заполнения', counMessage, function ( ) {
                $( '[href="#tab-pane-zakaz-page2"]' ).trigger( 'click' );
                for (let i in targetEl){
                    targetEl[i].addClass('has-error');
                }
            }, false );
            this._hideBunner( );
            return false;
        }
        if ( self._productCategory == 1 ) {
            let comp = $( '#Zakaz-product_size' ).val( );
            if ( !comp.length || ( comp.split( '*' ).length !== 2
                    && comp.split( 'Х' ).length !== 2
                    && comp.split( 'х' ).length !== 2
                    && comp.split( 'x' ).length !== 2
                    && comp.split( 'X' ).length !== 2 )
                    ) {
                let sz = $( '.mat-middle-zone' ).find( '[data-material-size]' ).attr( 'data-material-size' );
                let edit = {
                    label: 'Редактировать',
                    click: function ( ) {
                        $( '[href="#tab-pane-zakaz-page0"]' ).trigger( 'click' );
                        $( '#Zakaz-product_size' ).trigger( 'focus' );
                    }
                };
                let auto = {
                    label: 'Авто',
                    click: function ( ) {
                        $( '#Zakaz-product_size' ).val( $.fn.formatProceed( sz ) );
                        $( '[role="save-button"]' ).trigger( 'click' );
                    }
                };
                ////if ( _ZDEBUG ) console.log( sz );
                m_alert( 'Внимание!', 'Неправильно заполнено поле "Размер готового изделия"!', sz.length ? auto : edit, sz.length ? edit : false );
                this._hideBunner( );
                return false;
            }
        }
        return this._checkRaspakovkaUpakovkaError( );
    },
    _saveAjax: function ( form, requestDraft, onCloseClickEnd ) {
        //requestDraftUrl
        let self = this;
        let hasPodrayd = false;
        requestDraft = requestDraft ? true : false;
        if ( !this.options.isDisainer && !this._checkOtherError( ) )
            return false;
        let hasMaterials = form.get( 'Zakaz[materials][0][supplierType]' ) || form.get( 'Zakaz[materials][1][supplierType]' );
        for ( let pair of form.entries( ) ) {
            //if ( _ZDEBUG ) console.log( pair[0] + ', ' + pair[1] );
            hasPodrayd = hasPodrayd || pair[0].indexOf( '[podryad]' ) > -1;
        }
        //if ( _ZDEBUG ) console.log( hasPodrayd, hasMaterials );
        if ( !hasMaterials ) {
            form.append( 'Zakaz[materials]', '[]' );
        }
        if ( !hasPodrayd ) {
            form.append( 'Zakaz[podryad]', '[]' );
        }
        //if ( _ZDEBUG ) console.log( 'beforeSave', hasPodrayd );
        $.ajax( {
            type: 'post',
            url: requestDraft && self.options.requestDraftUrl ? self.options.requestDraftUrl : self.options.requestUrl,
            data: form,
            cache: false,
            contentType: false,
            processData: false,
            forceSync: false,
            complete: function ( ) {
//                        self.busy(false);
            },
            success: function ( data ) {
                //if ( _ZDEBUG ) console.log( data );
                if ( data.status === 'saved' ) {
                    self.replaceMainMenu( data.menu, data.label );
                    if ( $.isFunction( self.options.afterSave ) ) {
                        self.options.afterSave.call( self.element, data );
                    }
                    self.options.beforeClose = null;
                    if ( $.isFunction( onCloseClickEnd ) ) {
                        onCloseClickEnd.call( self );
                    }
                    self.close( );
                    $.fn.dropInfo( 'Заказ ' + ( data.lastZakaz ? ( '№ ' + data.lastZakaz ) : '' ) + ' ' + ( data.isNew ? 'добавлен' : 'сохранён' ) + ( requestDraft ? ' как черновик ' : '' ) + '!', 'success', 5000 );
                } else if ( data.status === 'error' ) {
                    self._hideBunner( );
                    let isVisible = false;
                    let notFoundEl = [ ];
                    let showTab = true;
                    let focusTriggered = false;
                    $.each( data.errors, function ( key, val ) {
                        let errorGroup = $( '#gtoup-control-' + key );
                        if ( errorGroup.length ) {
                            errorGroup
                                    .children( '.activeDD, input, select' )
                                    .addClass( 'hasError' )
                                    .tooltip( {container: self.element.parent( ), title: val[0]} );
                            if ( !isVisible ) {
                                let parentEl = $.fn.findParentByClass( $( '#gtoup-control-' + key ), 'tab-pane' );
                                if ( !parentEl.length ) {
                                    parentEl = $.fn.findParentByClass( $( '#gtoup-control-' + key ), 'header2' );
                                    if ( parentEl.length )
                                        showTab = false;
                                }
                                if ( parentEl.length ) {
                                    isVisible = true && showTab;
                                    if ( showTab )
                                        $( '[href="#' + parentEl.attr( 'id' ) + '"]' ).trigger( 'click' );
                                    if ( !focusTriggered ) {
                                        //if ( _ZDEBUG ) console.log( 'error-trigger-focus' );
                                        //if ( _ZDEBUG ) console.log( errorGroup.children( '.activeDD, input, select' ) );
                                        let el = errorGroup.children( '.activeDD' ).children( 'input' );
                                        if ( el.length ) {
                                            el.trigger( 'focus' );
                                            focusTriggered = true;
                                        } else {
                                            el = errorGroup.children( 'input, select' );
                                            if ( el.length ) {
                                                el.trigger( 'focus' );
                                                focusTriggered = true;
                                            }
                                        }

                                    }
                                } else {
                                    notFoundEl[notFoundEl.length] = {
                                        label: $.inArray( key, Object.keys( self.options.form.attributeLabels ) ) > -1 ? self.options.form.attributeLabels[key] : key,
                                        content: val[0]
                                    };
                                }
                            }
                        } else {
                            notFoundEl[notFoundEl.length] = {
                                label: $.inArray( key, Object.keys( self.options.form.attributeLabels ) ) > -1 ? self.options.form.attributeLabels[key] : key,
                                content: val[0]
                            };
                        }
                    } );
                    if ( notFoundEl.length ) {
                        let html = '<h4>Если ошибки повторятся обратитесь к системному администратору</h4>';
                        $.each( notFoundEl, function ( ) {
                            html += '<p>' + this.label + ' : ' + this.content + '<p>';
                        } );
                        m_alert( 'Ошибка в скрытых элементах', html, true, false );
                    }
                }
            },
            error: function ( jqXHR ) {
                $.fn.dropInfo( 'Ошибка сохранения', 'danger' );
                $.fn.dropInfo( jqXHR.responseText, 'danger' );
            }

        } );
    },
    __proceedSaveClick: function ( draft, saveFile, onCloseClickEnd ) {
        if ( this.options.requestUrl ) {
            this._showBunner( 'Сохранение...' );
            let form = new FormData( $( '#form-' + this.options.form.name ).get( 0 ) );
            if ( this.options.z_id )
                form.append( 'id', this.options.z_id );
            if ( saveFile && this.options.isCopy ) {
                form.append( 'copyFileFrom', this.options.isCopy );
            }
            if ( !this._jqXHRCount )
                this._saveAjax( form, draft, onCloseClickEnd );
            else
                this._onAllFileComlete = function ( ) {
                    this._saveAjax( form, draft, onCloseClickEnd );
                }
        } else {
            $.fn.dropInfo( 'requestUrl - не задан', 'danger' );
        }
    },
    _saveClick: function ( e, draft, onCloseClickEnd ) {
//            //if ( _ZDEBUG ) console.log(e);
        draft = draft ? true : false;
        let self = this;
        if ( draft ) {
//        new mDialog.m_alert( {
//            content: 'Новые данные <b style="color:red">НЕ СОХРАНЯТСЯ</b>!',
//            okClick: {
//                label: 'заказ ЗАКРЫТЬ',
//                click: function () {
//                    el.zakazAddEditController( 'closeAnyWhay' );
//                },
//                class: 'btn btn-red'
//            },
//            canselClick: {
//                label: 'ПРОДОЛЖИТЬ редактирование',
//                class: 'btn btn-gray-text-black'
//            },
//            showCloseButton: false
//        } );
            new mDialog.m_alert( {
                headerText: 'Внимание! Сохранение как черновик',
                content: 'Файлы <b style="color:red">НЕ СОХРАНЯТСЯ!</b>',
                okClick: {
                    label: 'сохранить ЧЕРНОВИК',
                    click: function ( ) {
                        self.__proceedSaveClick( draft );
                    },
                    class: 'btn btn-red'
                },
                canselClick: {
                    label: 'ПРОДОЛЖИТЬ редактирование',
                    class: 'btn btn-gray-text-black'
                },
                showCloseButton: false
            } );
//            new mDialog.m_alert( 'Внимание! Сохранение как черновик', 'Файлы не будут сохранены! Продолжить?', function () {
//                self.__proceedSaveClick( draft );
//            }, true, null, null, false );
        } else {
            if ( this.options.isCopy && ( $.isPlainObject( this._loadedFileList.des ) ? Object.keys( this._loadedFileList.des ).length : this._loadedFileList.des.length || $.isPlainObject( this._loadedFileList.main ) ? Object.keys( this._loadedFileList.main ).length : this._loadedFileList.main.length ) ) {
                m_alert( 'Внимание', 'Сохранить файлы из старого заказа?', {
                    label: 'Да',
                    click: function ( ) {
                        self.__proceedSaveClick( draft, true );
                    }
                }, {
                    label: 'Нет',
                    click: function ( ) {
                        self.__proceedSaveClick( draft );
                    }
                } );
            } else {
                this.__proceedSaveClick( draft, false, onCloseClickEnd );
            }
        }
    },
    _busyDown: function ( ) {
//            console.info('busy',this.busy);
        this.busy--;
        if ( this.busy === 0 ) {
            let self = this;
            $( '.invisible' ).removeClass( 'invisible' );
            this._hideBunner( );
            this._zakaz_material_init( );
            this._zakaz_file_init( );
            $.fn.dropInfo( 'Форма получена время запроса: ' + ( performance.now( ) - this.tm ).toFixed( 2 ) + 'ms', 'success' );
            $.fn.dropInfo( 'Заказ загружен', 'success' );
            if ( $( '#Zakaz-zak_id' ).val( ) ) {
//                    //if ( _ZDEBUG ) console.log($('#Zakaz-zak_id').next());
                //let tmp=$('#Zakaz-zak_id').next().children('input').activeDD('findByValue',$('#Zakaz-zak_id').val());
                let tmp = $( '#Zakaz-manager_id' ).val( );
                this._getCustomerContaktList.call( $( '#Zakaz-zak_id' ).next( ).children( 'input:first-child' ), {self: this, dontClear:true, afterUpdate: function ( ) {
                        if ( !tmp )
                            return;
//                            //if ( _ZDEBUG ) console.log(this);
                        let txt = $( this ).activeDD( 'findByValue', tmp );
                        if ( txt ) {
                            $( this ).val( txt.label );
                            $( '#Zakaz-manager_id' ).val( tmp );
                            $( this ).attr( 'data-key', tmp );
                            self._getCustomerManagerInfo.call( $( this ), {self: self} );
                        }
                    },
                    hideMessage: true
                } );
            }
            if ( this.options.toTempStoreUrl && this.options.form.fields.tmpName && this._sendFreq ) {
                let self = this;
                this._sendInterval = setInterval( function ( ) {
                    self._storeTemp( );
                }, this._sendFreq * 5000 );
            }
            $( 'input' ).each( function ( ) {
                if ( $( this ).attr( 'type' ) !== 'hidden' && $( this ).attr( 'type' ) !== 'radio' && $( this ).attr( 'type' ) !== 'button' )
                    $( this ).enterAsTab( );
                ////if ( _ZDEBUG ) console.log($(this));
            } );
            $( 'input,textarea' ).focusout( function ( ) {
                self._resetFrq = true;
            } );
            $( 'a,input,button' ).click( function ( ) {
                self._resetFrq = true;
            } );
            if ( this.options.form.fields.re_print ) {
                $( '#zakaz_material_profit' ).val( 0 ); //.removeAttr('disabled');
                $( '#profitTypeRadio' ).radioButton( 'option', 'disabled', true );
                $( '#profitTypeRadio' ).radioButton( 'value', 0 );
            }
            if ( $( '#Zakaz-production_id' ).attr( 'data-category' ) === '1' && $.isFunction( this.__block10Recalc ) )
                this.__block10Recalc( {data: {self: this}} );
            if ( $( '#Zakaz-production_id' ).attr( 'data-category' ) === '2' && $.isFunction( this.__block11Recalc ) )
                this.__block11Recalc( {data: {self: this}} );
            if ( this._isOpl || this.options.form.fields.re_print ) {
                $( '#tab-pane-zakaz-page0' ).css( 'position', 'relative' ).append( $( '<img>' ).attr( {
                    src: '/pic/oplacheno.png',
                    height: '140px'
                } ).css( {
                    position: 'absolute',
                    top: '145px',
                    right: '8px'
                } ).mousedown( function ( e ) {
                } ) );
            }
            //Zakaz-stage
            if ( !this.options.isDisainer ) {
                let img_compl = $( '<img>' ).attr( {
                    src: 'pic/sdano.png',
                    height: '140px'
                } ).css( {
                    position: 'absolute',
                    top: 68,
                    left: 775,
                    display: 'none',
                    title: 'Заказ сдан',
                    'pointer-events': 'none'
                } ).appendTo( $( '#tab-pane-zakaz-page0' ) );
                if ( $( '#Zakaz-stage' ).val( ) == '8' ) {
                    //$('#Zakaz-stage').parent().addClass('bg-success');
                    img_compl.css( 'display', 'block' );
                }
                $( '#Zakaz-stage' ).change( function ( ) {
                    if ( $( this ).val( ) == '8' ) {
                        //$(this).parent().addClass('bg-success');
                        img_compl.css( 'display', 'block' );
                    } else {
                        //$(this).parent().removeClass('bg-success');
                        img_compl.css( 'display', 'none' );
                    }
                } );
            }
            $( '#Zakaz-number_of_copies' ).onlyNumeric( {allowStar: true, defaultVal: ''} );
            $( '#Zakaz-number_of_copies1' ).onlyNumeric( {allowStar: true, defaultVal: ''} );
            this._checkComments( );
            $( '#Zakaz-design_comment,#Zakaz-proizv_comment' ).change( {self: this}, function ( e ) {
                e.data.self._checkComments( );
            } );
            this._checkMaterialCount( );
            let m_count = $( '.mat-middle-zone' ).children( 'div:first-child' ).children( ).length - 1;
            if ( m_count > 0 )
                this.__blockRecalMainParam.call( this, '', false );
            if ( m_count > 1 )
                this.__blockRecalMainParam.call( this, '1', false );
//                if (this._productCategory===3 && $('#uflak-tirage').length){
//                    if ($(this).val())
//                        $('#uflak-tirage').val(parseInt($(this).val())+100);
//                    else
//                        $('#uflak-tirage').val(0);
//                }
            if ( self.options.isDisainer ) {
                $( '.main-content' ).height( $( '.main-content' ).height( ) + 200 );
                $( '.ui-dialog-content' ).css( 'min-height', 700 );
                self.element.parent( ).css( 'top', $( window ).height( ) / 2 - self.element.parent( ).height( ) / 2 );
            }
            new NEWFUNCTION.FixTable( '#executers_table' );
        }
    },
    _checkMaterialCount: function ( ) {
        let m_count = $( '.mat-middle-zone' ).children( 'div:first-child' ).children( ).length - ( ( this._productCategory > 1 && this._productCategory != 3 ) ? 1 : 1 );
        let tmpCopy = $( '#Zakaz-number_of_copies1' ).val( );
        //if ( _ZDEBUG ) console.log( '_checkMaterialCount:_productCategory', this._productCategory );
        //if ( _ZDEBUG ) console.log( '_checkMaterialCount:.mat-middle-zone', $( '.mat-middle-zone' ).children( 'div:first-child' ).children( ).length );
        //if ( _ZDEBUG ) console.log( '_checkMaterialCount:m_count', m_count );
        if ( m_count > 0 && ( !tmpCopy || !tmpCopy.length || tmpCopy == 0 || m_count > 1 ) ) {
            $( '#zakaz-select-material' ).attr( 'disabled', true ).addClass( 'disabled' );
            return true;
        } else {
            $( '#zakaz-select-material' ).removeAttr( 'disabled' ).removeClass( 'disabled' );
            return false;
        }
    },
    _checkComments: function ( ) {
        if ( ( $( '#Zakaz-design_comment' ).val( ).length || $( '#Zakaz-proizv_comment' ).val( ).length ) && !$( '.info-warning' ).length ) {
            //zakaz_dialog
            let img_tmp = $( '<img>' ).addClass( 'info-warning' ).attr( {
                src: '/pic/warning-pixabay.png',
                title: 'Имеются комментарии!'
            } ).appendTo( $( '[href="#tab-pane-zakaz-page' + ( this.options.isDisainer ? '7' : '6' ) + '"]' ) ).tooltip( );
        } else {
            $( '.info-warning' ).remove( );
        }
    },
    replaceMainMenu: function ( menu, label, selector ) {
        selector = selector ? selector : '.nav-mess-main';
        let self = this;
        if ( menu && label ) {
            $( selector ).children( '.dropdown-menu' ).empty( ).html( $( menu ).html( ) );
            $( selector ).children( '.dropdown-toggle' ).empty( ).html( label + '<b class="caret"></b>' );
        }
        $.fn.enablePopover( );
        if ( $.isFunction( this.options.storedZClick ) ) {
            $( '.stored-z' ).click( this.options.storedZClick );
        }
        if ( $.isFunction( this.options.storedZContextMenu ) ) {
            $( '.stored-z' ).contextmenu( this.options.storedZContextMenu );
        }
        if ( $.isFunction( this.options.storedZRemoveAll ) ) {
            $( '.stored-z-remove-all' ).click( this.options.storedZRemoveAll );
        }
    },
    _storeTemp: function ( ) {
        let form = new FormData( $( '#form-' + this.options.form.name ).get( 0 ) );
        let uri = URI.parse( window.location.href );
//            //if ( _ZDEBUG ) console.log('URI',uri);
        form.append( 'speed', this._sendFreq );
        form.append( 'id', this.options.z_id );
        if ( this._resetFrq || this._jqXHRCount ) {
            this._resetFrq = false;
            form.append( 'reset', true );
        }
        $.ajax( {
            type: 'post',
            url: this.options.toTempStoreUrl,
            data: form,
            cache: false,
            contentType: false,
            processData: false,
            forceSync: false,
            success: function ( data ) {
//                    //if ( _ZDEBUG ) console.log('_storeTemp',data);
            }
        } );
    },
    _dorequest: function ( reDraw ) {
        if ( this.options.requestUrl ) {
            let self = this, opt = {};
            this.tm = performance.now( ),
                    this.busy++;
            $.fn.dropInfo( 'Запрос данных формы ', 'info' );
            if ( this.options.z_id )
                opt.id = this.options.z_id;
            if ( self.options.isCopy ) {
                //if ( _ZDEBUG ) console.log( '_dorequest:isToCopy' );
                opt.isToCopy = true;
                opt.copyAsReprint = this.options.copyAsReprint;
            }
            if ( this.options.isDisainer ) {
                opt.isDisainer = true;
            }
            this._showBunner( );
            $.post( this.options.requestUrl, opt ).done( function ( answ ) {
                console.info( 'answ', answ );
                if ( answ.status === 'error' ) {
                    self.options.beforeClose = null;
                    self.close( );
                    new m_alert( answ.headerText ? answ.headerText : 'Ошибка сервера', answ.errorText, true, false );
                } else {
                    self.options.form = {
                        name: answ.formName,
                        fields: !self.options.restore ? answ.values : $.extend( {}, answ.values, self.options.restore ),
                        attrList: answ.attrList,
                        attributeLabels: answ.attributeLabels
                    }
                    if ( self.options.isCopy ) {
                        self.options.z_id = null;
                        self.options.form.fields.id = null;
                        if ( $.isArray( self.options.form.fields.materials ) ) {
                            $.each( self.options.form.fields.materials, function ( ) {
                                delete this.id;
                                delete this.zakaz_id;
                            } );
                        }
                        if ( $.isArray( self.options.form.fields.podryad ) ) {
                            $.each( self.options.form.fields.podryad, function ( ) {
                                delete this.id;
                                delete this.zakaz_id;
                            } );
                        }
                    }
                    self._loadedFileList = answ.allFileList;
                    self._isOpl = answ.isOpl;
                    self._productCategory = !self.options.restoreProductCategory ? answ.productCategory : self.options.restoreProductCategory;
                    self._productCategory2 = !self.options.restoreProductCategory2 ? answ.productCategory2 : self.options.restoreProductCategory2;
                    if ( $.type( self.options.form.fields.post_print_rezka ) === 'undefined' ) {
                        self.options.form.fields.post_print_rezka = 1;
                    }
                    if ( $.type( self.options.form.fields.method_of_payment ) === 'undefined' ) {
                        self.options.form.fields.method_of_payment = 1;
                    }
                    if ( answ.executersList )
                        self.executersList = answ.executersList;
                    if ( reDraw )
                        self._reDraw( );
                    if ( self.options.form.fields.podryad.length && !self.options.isDisainer )
                        self._executersDrawLoaded( );
                    if ( self.options.form.fields.materials.length && !self.options.isDisainer )
                        self._zakaz_materialDrawLoaded( );
                    self.element.css( 'min-height',
                            ( self.uiDialog.innerHeight( )
                                    - self.uiDialogButtonPane.outerHeight( )
                                    - self.uiDialogTitlebar.outerHeight( ) + 43 )
                            );
                    self.element.parent( ).css( 'top', $( window ).height( ) / 2 - self.element.parent( ).height( ) / 2 );
                    self._busyDown( );
                }
            } ).fail( function ( answ ) {
                //if ( _ZDEBUG ) console.log( answ );
                m_alert( 'Ошибка', 'Неудалось загрузить данные.<br>Обновите страницу и попробуйте снова.', true, false );
                self.close( );
            } );
        } else {
            console.warn( '_dorequest: Не задан requestUrl' );
        }
    },
    _createFieldName: function ( name ) {
        if ( name !== '' )
            return this.options.form.name + '[' + name + ']';
        else
            return '';
    },
    _createFieldId: function ( name ) {
        return this.options.form.name + '-' + name
    },
    _createInput: function ( name, param, events, setDefault ) {
        setDefault = $.type( setDefault ) !== 'undefined' ? setDefault : true;
        let rv = $( '<input>' ).attr( $.extend( {}, {
            id: this._createFieldId( name ),
            name: this._createFieldName( name )
        }, param ? param : {} ) );
        if ( $.type( events ) === 'object' ) {
            $.each( events, function ( key, val ) {
                if ( $.isFunction( val ) )
                    rv.on( key, val );
                else if ( $.type( val ) === 'array' ) {
                    if ( val.length > 1 ) {
                        rv.on( key, val[0], val[1] );
                    }
                }
            } );
        }
        if ( setDefault && $.type( this.options.form.fields[name] ) !== 'undefined' ) {
            rv.val( this.options.form.fields[name] );
        }
        return rv;
    },
    attributeLabel: function ( name, def ) {
        if ( name ) {
            return this.options.form.attributeLabels[name] ? this.options.form.attributeLabels[name] : def ? def : name;
        } else {
            if ( def ) {
                console.warn( 'attributeLabel параметр name не указан используется def' );
                return def;
            } else
                console.error( 'attributeLabel параметр name должен быть указан' );
        }
    },
    _reDrawTitle: function ( ) {
        let val = this.options.form.fields.id ? this.options.form.fields.id : 'Новый';
        this.uiDTitle.empty( );
        let tag = this.options.form.fields.id ? $( '<input>' )
                .attr( {
                    'readonly': true,
                    name: this._createFieldName( 'id' )
                } )
                .css( 'width', 100 )
                .val( val ) :
                $( '<span>' )
                .addClass( 'dialog-form-control' )
                .css( 'width', 100 )
                .text( val );
        this.uiDTitle
                .append( $( '<label>' ).text( 'Заказ№ ' ) )
                .append( tag );
        //{name:'ourmanagername',labelClass:'dialog-col-100',controlClass:'dialog-col-120',readonly:true}
        let tag2 = this.options.form.fields.id ? $( '<input>' )
                .attr( {
                    'readonly': true,
                    name: this._createFieldName( 'ourmanagername' )
                } )
                .css( {'width': 200} )
                .val( this.options.form.fields.ourmanagername ? this.options.form.fields.ourmanagername : '' ) :
                $( '<span>' )
                .addClass( 'dialog-form-control' )
                .css( 'width', 200 )
                .text( this.options.form.fields.ourmanagername ? this.options.form.fields.ourmanagername : '' );
        this.uiDTitle
                .append( $( '<label>' ).text( 'Менеджер: ' ).css( {'margin-left': '10px'} ) )
                .append( tag2 );
    },
    //_createInputControlGroup:function(label,name,labelClass,nameClass,datePic,addOn){
    _createInputControlGroup: function ( opt ) {
        opt = $.extend( {
            label: null, name: '', labelClass: false, controlClass: false, id: null,
            datePicker: false, timePicker: false, addOn: false, defValue: false, groupSize: false, onChange: null, onFocus: null, onFocusout: null,
            labelSize: false, controlSize: 150, readonly: false, activeDD: false,
            click: false, createHiddenInput: false, asInfo: false, activeDDUrlVarName: false,
            inline: true, addGoupClass: false, controlTag: 'input', controlAttr: {}, hideAll: false, wantAdd: false, wantAddTitle: false
        }, opt );
        if ( opt.controlTag === 'textarea' )
            opt.controlAttr = {rows: 4};
        if ( opt.hideAll )
            opt.createHiddenInput = true;
        opt.controlTag = '<' + opt.controlTag + '>';
//            //if ( _ZDEBUG ) console.log(opt);
        let value = $.type( this.options.form.fields[opt.name] ) !== 'undefined' && this.options.form.fields[opt.name] !== null && this.options.form.fields[opt.name] !== '' ? this.options.form.fields[opt.name] : opt.defValue !== false ? opt.defValue : false;
        //let value=$.type(this.options.form.fields[opt.name])!=='undefined'&&this.options.form.fields[opt.name]!==null&&opt.defValue===false?this.options.form.fields[opt.name]:opt.defValue!==false?opt.defValue:false;
//            //if ( _ZDEBUG ) console.log(opt.name,value);
        let lbl = $( '<label>' ).text( this.attributeLabel( opt.name, opt.label ) ), tmpCont = null;
        let rVal = $( '<div>' )
                .addClass( 'dialog-control-group' + ( opt.inline ? '-inline' : '' ) + ( opt.addGoupClass ? ( ' ' + opt.addGoupClass ) : '' ) )
                .attr( {id: 'gtoup-control-' + opt.name} );
        if ( opt.labelClass )
            lbl.addClass( opt.labelClass );
        if ( opt.label !== false && !opt.hideAll )
            rVal.append( lbl );
        let id = opt.id === null ? this.options.form.name + "-" + opt.name : opt.id;
        if ( opt.id )
            console.warn( 'createInputControlGroup', 'id задан вручную: ' + opt.id );
        let hiddenInp = opt.createHiddenInput ? $( '<input>' ).attr( 'type', 'hidden' ) : null;
        let inp = $( opt.controlTag ).attr( opt.controlAttr );
        if ( hiddenInp ) {
            hiddenInp.attr( {'name': this._createFieldName( opt.name ), id: id} );
            if ( $.isFunction( opt.onChange ) )
                hiddenInp.change( {self: this}, opt.onChange );
            hiddenInp.val( value !== false ? value : 0 );
        } else {
            inp.attr( {'name': this._createFieldName( opt.name ), id: id} );
            if ( this.options.validateUrl )
                inp.focusout( {self: this, targetEl: inp}, this._validate );
            if ( $.isFunction( opt.click ) )
                inp.click( {self: this}, opt.click );
            if ( $.isFunction( opt.onChange ) )
                inp.change( {self: this}, opt.onChange );
        }
        if ( $.isFunction( opt.onFocus ) )
            inp.focus( {self: this, hiddenInp: hiddenInp}, opt.onFocus );
        if ( $.isFunction( opt.onFocusout ) )
            inp.focusout( {self: this, hiddenInp: hiddenInp}, opt.onFocusout );
        if ( opt.asInfo ) {
            if ( hiddenInp )
                rVal.append( hiddenInp );
            let asInfo = $( '<input>' )
                    .addClass( 'dialog-form-control' + ( opt.controlClass ? ( ' ' + opt.controlClass ) : '' ) )
                    .attr( {
                        id: opt.id === null ? 'info-' + id : id,
                        readonly: true,
                        tabindex: ( ( typeof opt.noTab === 'undefined' || this.options.z_id ) && !opt.noTabImportant ) ? this.options.tabIndex++ : -1
                    } )
                    .val( value !== false && opt.activeDD === false ? value : '' );
            if ( !opt.controlClass && opt.controlSize )
                asInfo.width( opt.controlSize );
            rVal.append( asInfo );
//                $.fn.dropInfo('Добавлено поле режим asInfo '+opt.name+': id='+id+(value!==false&&value!==null?(' значение: '+value):''));
            return rVal;
        }

        if ( opt.readonly )
            inp.attr( 'readonly', true );
        if ( !this.options.isInited && $.type( opt.activeDD ) === 'object' ) {
            if ( !opt.activeDD.requestUrl && opt.activeDDUrlVarName )
                opt.activeDD.requestUrl = this.options[opt.activeDDUrlVarName] ? this.options[opt.activeDDUrlVarName] : null;
            if ( value !== false && value !== null ) {
                this.busy++;
                let self = this, tm = performance.now( );
//                    $.fn.dropInfo('Запрос списка для '+opt.name,'info');
                let onReady = opt.activeDD.onReady;
                opt.activeDD.onReady = function ( ) {
                    let tmp = $( this ).activeDD( 'findByValue', parseInt( value ) );
//                        console.info('vl_set',opt.name,value,tmp);
                    if ( hiddenInp )
                        hiddenInp.val( value );
                    if ( hiddenInp && tmp ) {
                        inp.val( tmp.label );
                    } else if ( tmp ) {
                        inp.val( tmp.label );
                    }
                    if ( tmp )
                        inp.attr( 'data-key', tmp.value );
                    if ( tmp && hiddenInp && $.type( tmp.category ) !== 'undefined' ) {
                        hiddenInp.attr( 'data-category', tmp.category );
                    } else {
                        if ( hiddenInp && hiddenInp.attr( 'data-category' ) )
                            hiddenInp.removeAttr( 'data-category' );
                    }
                    ;
//                        $.fn.dropInfo('Список получен '+opt.name+' время запроса: '+(performance.now()-tm).toFixed(2)+'ms','success');
                    if ( $.isFunction( onReady ) )
                        onReady.call( this, tmp );
                    self._busyDown( );
                };
            }
            tmpCont = $( '<div>' ).addClass( 'activeDD invisible' ).append( inp );
            inp.activeDD( $.extend( {}, opt.activeDD, {appendTo: this.element.parent( )} ) );
        } else if ( value !== false )
            inp.val( value );
        if ( !opt.readonly && ( !opt.noTab || !this.options.z_id ) && !opt.noTabImportant ) {
            inp.attr( 'tabindex', this.options.tabIndex++ );
        } else {
            inp.attr( 'tabindex', -1 );
        }
        if ( opt.controlClass ) {
            if ( !tmpCont )
                inp.addClass( opt.controlClass );
            else
                tmpCont.addClass( opt.controlClass );
        }
        if ( hiddenInp )
            rVal.append( hiddenInp );
        if ( !tmpCont ) {
            rVal.append( inp );
            if ( opt.controlClass === false && opt.controlSize )
                inp.width( opt.controlSize );
        } else {
            rVal.append( tmpCont );
            inp.width( opt.controlSize );
        }
        if ( opt.addOn )
            rVal.append( $( '<span>' ).addClass( 'add-on' ).text( opt.addOn ) );
//            $.fn.dropInfo('Добавлено поле '+opt.name+': id='+id);
        this.isInited = true;
        inp.addClass( 'invisible' );
        if ( $.isFunction( opt.onChange ) ) {
            if ( !hiddenInp )
                opt.onChange.call( inp, {data: {self: this}} );
            else
                opt.onChange.call( hiddenInp, {data: {self: this}} );
        }
        if ( $.type( opt.activeDD ) === 'object' && $.isFunction( opt.wantAdd ) ) {
            let addW = $( '<button>' ).addClass( 'b-button b-plus b-small' ).text( '+' );
            rVal.append( addW );
            if ( opt.wantAddTitle )
                addW.attr( 'title', opt.wantAddTitle );
            opt.wantAdd.call( addW, inp );
            //addW.click({self:this},opt.wantAdd);
        }
        if ( opt.datePicker ) {
            if ( $.type( opt.datePicker ) === 'object' ) {
                inp.datepicker( opt.datePicker );
            } else {
                inp.datepicker( );
            }
        } else if ( opt.timePicker ) {
            if ( $.type( opt.timePicker ) === 'object' ) {
                inp.timePicker( opt.timePicker );
            } else {
                inp.timePicker( );
            }
        }
        if ( !opt.hideAll || !hiddenInp )
            return rVal;
        else
            return hiddenInp;
    },
    _removePopOver: function ( el ) {
        let doWith = el.attr( 'type' ) === 'hidden' ? el.next( ) : el;
        $( ).removeAttr( )
        doWith
                .tooltip( 'destroy' )
                .removeClass( 'hasError' );
    },
    __validate: function ( el ) {
//            //if ( _ZDEBUG ) console.log('validate',el);
        let arr = el.attr( 'id' ).split( '-' ), self = this;
        let attrName = arr.length > 1 ? arr[1] : arr.length > 0 ? arr[0] : null;
        let form = new FormData( );
        if ( this.options.z_id )
            form.append( 'id', this.options.z_id );
        form.append( 'attrName', attrName );
        form.append( 'value', el.val( ) );
        //
        if ( arr !== null ) {
//                $.post(self.options.validateUrl,{attrName:attrName,value:el.val()}).done(function(data){
            $.ajax( {
                type: 'post',
                url: self.options.validateUrl,
                data: form,
                cache: false,
                contentType: false,
                processData: false,
                forceSync: false,
                complete: function ( ) {
//                        self.busy(false);
                },
                success: function ( data ) {
                    //if ( _ZDEBUG ) console.log( 'validate', data );
                    if ( data.status === 'ok' ) {
                        self._removePopOver( el );
                    } else if ( data.errors ) {
                        $( '#gtoup-control-' + attrName )
                                .children( '.activeDD, input, select' )
                                .addClass( 'hasError' )
                                .tooltip( 'hide' )
                                .tooltip( 'destroy' )
                                .tooltip( {container: self.element.parent( ), title: data.errors[0]} );
                    } else if ( data.errorText ) {

                    }
                }
            } );
        } else {
            console.warn( 'validate не удалось определить имя атрибута.' );
        }
    },
    _validate: function ( e ) {
        e.data.self.__validate( e.data.targetEl );
    },
    _createDialogRow: function ( opt ) {
        opt = $.extend( {class: 'dialog-row', items: [ ]}, opt );
        let rVal = $( '<div>' ), self = this;
        if ( opt.class )
            rVal.addClass( opt.class );
        $.each( opt.items, function ( ) {
            rVal.append( self._createInputControlGroup( this ) );
        } );
        return rVal;
    },
    _createAllRows: function ( opt ) {
        opt = $.extend( {class: 'header2', rows: [ ]}, opt );
        let rVal = $( '<div>' ).addClass( opt.class ), self = this;
        $.each( opt.rows, function ( ) {
            rVal.append( self._createDialogRow( this ) );
        } );
        return rVal;
    },
    _getCustomerManagerInfo: function ( dt ) {
        $( '#info-' + dt.self.options.form.name + '-email' ).val( 'нет' );
        $( '#info-' + dt.self.options.form.name + '-phone' ).val( 'нет' );
        if ( dt.self.options.customerManageDetailUrl ) {
            let id = $( this ).attr( 'data-key' ) ? $( this ).attr( 'data-key' ) : 0;
            if ( id ) {
                $( '#' + dt.self.options.form.name + '-manager_id' ).val( id );
                $.post( dt.self.options.customerManageDetailUrl, {id: id} ).done( function ( answ ) {
                    if ( answ.status === 'ok' ) {
                        $( '#info-' + dt.self.options.form.name + '-email' ).val( answ.value.mail ? answ.value.mail : 'нет' );
                        $( '#info-' + dt.self.options.form.name + '-phone' ).val( answ.value.phone ? answ.value.phone : 'нет' );
                    } else {
                        m_alert( 'Ошибка сервера', answ.errorText, true, false );
                    }
                } ).fail( function ( er1 ) {
                    if ( $.type( er1.responseJSON ) === 'object' )
                        m_alert( er1.responseJSON.name, er1.responseJSON.message, true, false );
                    else
                        m_alert( "Ошибка сервера", er1.responseText, true, false );
                } );
            } else {
                $( '#' + dt.self.options.form.name + '-manager_id' ).val( 0 );
            }
        } else
            console.warn( '_getCustomerManagerInfo не задан URL:customerManageDetailUrl' );
    },
    _getCustomerContaktList: function ( dt ) {
        let id = $( this ).attr( 'data-key' ) ? $( this ).attr( 'data-key' ) : 0;
        let aDD = $( '#' + dt.self.options.form.name + '-manager_id' ).next( ).children( 'input:first-child' );
        let hideMessage = dt.hideMessage === true;
        let self = this;
        //if ( _ZDEBUG ) console.log( '_getCustomerContaktList', this, aDD );
//            //if ( _ZDEBUG ) console.log($('#'+dt.self.options.form.name+'-manager_id').next().children('input:first-child'));
//            if (!$('#Zakaz-post_print_firm_name').val()) $('#Zakaz-post_print_firm_name').val($(this).val());
        $( '#' + dt.self.options.form.name + '-zak_id' ).val( id );
        $( '#info-' + dt.self.options.form.name + '-email' ).val( 'нет' );
        $( '#info-' + dt.self.options.form.name + '-phone' ).val( 'нет' );
        //$('#'+dt.self.options.form.name+'-manager_id')
        if ( $.isFunction( dt.afterUpdate ) )
            aDD.activeDD( 'option', 'afterUpdate', dt.afterUpdate );
        if ( id ) {
            aDD.activeDD( 'option', 'otherParam', {id: id} )
                    .activeDD( 'update', null, hideMessage );
            if (dt.dontClear!==true)
            aDD.val( '' ).removeAttr( 'data-key' );
        } else {
            aDD.activeDD( 'option', 'source', [ ] ).val( '' ).removeAttr( 'data-key' );
        }
        $( '#Zakaz-zakUrFace' ).next( ).children( 'input:first-child' )
                .activeDD( 'option', 'otherParam', {tableName: 'zakUrFace_list', zakazchikId: id} )
//                .activeDD( 'option', 'afterUpdate', function () {
//                    this.activeDD( 'findByValue', self.options.form.fields.zakUrFace ? self.options.form.fields.zakUrFace : 0, true );
//                } )
                .activeDD( 'update' );
    },
    _createSubTitle: function ( ) {
        let fields = this.options.form.fields;
        let self = this;
        ////if ( _ZDEBUG ) console.log('_createSubTitle',fields);
        return this._createAllRows( {
            rows: [
                {
                    items: [
                        {name: 'dateofadmission', labelClass: 'dialog-col-80', controlClass: 'dialog-col-100', noTab: true, datePicker: !this.options.isDisainer ? {
                                dateFormat: this.options.dateFormat,
                                defaultDate: new Date( )
                            } : null,
                            defValue: fields.dateofadmission ? fields.dateofadmission : false,
                            readonly: this.options.isDisainer,
                            onFocusout: function () {
                                $( this ).attr( 'tabindex', '-1' );
                            }
                        },
                        {name: 'deadline', labelClass: 'dialog-col-100', controlClass: 'dialog-col-100', noTab: true, datePicker: !this.options.isDisainer ? {
                                dateFormat: this.options.dateFormat,
                            } : null,
                            defValue: fields.deadline ? fields.deadline : false,
                            readonly: this.options.isDisainer,
                            onFocusout: function () {
                                $( this ).attr( 'tabindex', '-1' );
                            }
                        },
                        {name: 'deadline_time', labelClass: 'dialog-col-80', controlClass: 'dialog-col-80', noTab: true,
                            timePicker: !this.options.isDisainer ? {step: 15, hourFrom: 8, hourTo: 23, startValue: '18:00'} : false,
                            defValue: fields.deadline ? fields.deadline_time : false,
                            readonly: this.options.isDisainer,
                            onFocusout: function () {
                                $( this ).attr( 'tabindex', '-1' );
                            }
                        },
                        {name: 'ourmanager_id', labelClass: 'dialog-col-100', controlClass: 'dialog-col-120', noTab: true, hideAll: true, readonly: this.options.isDisainer},
                        !this.options.isDisainer ? {name: 'zak_id', labelClass: 'dialog-col-100', controlClass: 'dialog-col-220', controlSize: 180, noTab: true,
                            onFocusout: function () {
                                $( this ).attr( 'tabindex', '-1' );
                            },
                            activeDD: this.options.customerListUrl ? {
                                requestUrl: this.options.customerListUrl,
                                loadPicUrl: this.options.bigLoaderPicUrl ? this.options.bigLoaderPicUrl : null,
                                strictly: true,
                                onClickOrChange: [ {self: this}, this._getCustomerContaktList ],
                            } : false,
                            wantAdd: !this.options.isDisainer ? function ( input ) {
                                let opt = $.extend( {}, self.options.addCustomerOptions );
                                opt.simpleForm.requestUpdateParent = function ( t, data ) {
                                    //if ( _ZDEBUG ) console.log( data );
                                    input.activeDD("flashcache");
                                    input.activeDD( "update", function ( ) {
                                        if ( $.type( data ) === 'object' ) {
                                            let txt = input.activeDD( 'findByValue', data.firm_id );
                                            if ( txt ) {
                                                input.val( txt.label );
                                                input.attr( 'data-key', data.firm_id );
                                                self._addFirmComplete( txt );
                                            }
                                        }
                                    } );
                                };
                                opt.modal = true;
                                opt.simpleForm.modal = true;
                                this.firm_addchange( opt );
                            } : false,
                            wantAddTitle: !this.options.isDisainer ? 'Добавить фирму заказчика' : false,
                            createHiddenInput: this.options.customerListUrl ? true : false,
                        } : {name: 'zak_idText', labelClass: 'dialog-col-100', controlClass: 'dialog-col-220', readonly: true, controlSize: 180, noTab: true},
                    ]
                },
                {
                    items: [
                        !this.options.isDisainer ? {name: 'manager_id', labelClass: 'dialog-col-100', controlClass: 'dialog-col-180', noTab: true,
                            cacheable:false,
                            cacheKey:'manager_id_DD',
                            onFocusout: function () {
                                $( this ).attr( 'tabindex', '-1' );
                            },
                            activeDD: this.options.customerListUrl ? {
                                requestUrl: this.options.customerListUrl,
                                loadPicUrl: this.options.bigLoaderPicUrl ? this.options.bigLoaderPicUrl : null,
                                strictly: true,
                                autoLoad: false,
                                onClickOrChange: [ {self: this}, this._getCustomerManagerInfo ]
                            } : false,
                            wantAdd: !this.options.isDisainer ? function ( input ) {
                                let opt = $.extend( {}, self.options.addCustomerOptions );
                                opt.defaultActionName = 'change';
                                opt.beforeOpen = function ( ) {
                                    if ( !$( '#Zakaz-zak_id' ).val( ) || $( '#Zakaz-zak_id' ).val( ) == 0 ) {
                                        return false;
                                    }
                                };
                                opt.actionParams = function ( ) {
                                    return {id: $( '#Zakaz-zak_id' ).val( )};
                                };
                                opt.simpleForm.width = 995;
                                opt.simpleForm.modal = true;
                                opt.simpleForm.firm_id = '#Zakaz-zak_id';
                                opt.simpleForm.afterInit = function ( e ) {
                                    //if ( _ZDEBUG ) console.log( this, e );
                                    this.element.multiTabController( {
                                        requestUrl: self.options.editForm.ajaxlist_zakone,
                                        headerText: 'Заказчики',
                                        pointPicUrl: self.options.editForm.pointPicUrl,
                                        loadPicUrl: self.options.editForm.loadPicUrl,
                                        contentBackgroundColor: '#c4c7c7',
                                        bodyBackgrounColor: '#fff',
                                        firm_id: '#curfirm_id',
                                        pjaxId: self.options.editForm.pjaxId
                                    } );
                                };
                                opt.simpleForm.requestUpdateParent = function ( t, data ) {
                                    //if ( _ZDEBUG ) console.log( 'Обновляем DD' );
                                    input.activeDD("flashcache");
                                    input.activeDD( "update" );
                                };
                                this.firm_addchange( opt );
                            } : false,
                            wantAddTitle: !this.options.isDisainer ? 'Изменить фирму заказчика' : false,
                            createHiddenInput: this.options.customerListUrl ? true : false,
                        } : {name: 'manager_id_text', labelClass: 'dialog-col-100', controlClass: 'dialog-col-180', readonly: true},
                        !this.options.isDisainer ? {label: 'Тел.:', name: 'phone', labelClass: 'dialog-col-100', controlClass: 'dialog-col-140', asInfo: true, noTabImportant: true, }
                        : {label: 'Тел.:', name: 'manager_phone_text', labelClass: 'dialog-col-100', controlClass: 'dialog-col-120', asInfo: true, noTab: true},
                        !this.options.isDisainer ? {label: 'E-mail:', name: 'email', labelClass: 'dialog-col-100', controlClass: 'dialog-col-220', asInfo: true, noTabImportant: true}
                        : {label: 'E-mail:', name: 'manager_email_text', labelClass: 'dialog-col-100', controlClass: 'dialog-col-220', asInfo: true, noTab: true},
                    ]
                },
                {
                    class: 'dialog-row border',
                    items: [ ]
                },
                {
                    class: 'dialog-row center-block bg-gray',
                    items: !this.options.isDisainer ? [
                        {defValue: '0', name: 'total_coast', labelClass: '', controlClass: 'dialog-col-80', addOn: 'руб.', onChange: this._recalculateAll, readonly: true},
                        {defValue: '0', name: 'spending', labelClass: '', controlClass: 'dialog-col-80', addOn: 'руб.', onChange: this._recalculateAll, readonly: true},
                        {defValue: '0', name: 'spending2', labelClass: '', controlClass: 'dialog-col-80', addOn: 'руб.', onChange: this._recalculateAll, readonly: true},
                        {defValue: '0', name: 'profit', labelClass: '', controlClass: 'dialog-col-80', addOn: 'руб.', readonly: true}
                    ] : [ ]
                }
            ]} );
    },
    _addFirmComplete: function ( dt ) {
        //if ( _ZDEBUG ) console.log( dt );
        //Zakaz-zak_id
        let self = this;
        let options = {
            headerText: 'Клиент добавить',
            firm_id: this.options.editForm.firm_id,
            id_associated_column: this.options.editForm.id_associated_column,
            id_column_with_record_id: this.options.editForm.id_column_with_record_id,
            form_Id: this.options.editForm.form_Id,
            baseActionName: this.options.editForm.baseActionName,
            controller: this,
            requestUrl: this.options.editForm.requestUrl,
            loadPicUrl: this.options.editForm.loadPicUrl,
            width: this.options.editForm.width,
            requestOtherParam: {'req': {
                    name: 'change' + this.options.editForm.baseActionName,
                }, id: dt.value},
            loadPicUrl: this.options.editForm.loadPicUrl,
            pointPicUrl: this.options.editForm.pointPicUrl,
            onTabValidate: this.options.editForm.onTabValidate,
            'address': this.options.editForm.address,
            ks: this.options.editForm.ks,
            okpo: this.options.editForm.okpo,
            bank: this.options.editForm.bank,
            contentBackgroundColor: this.options.editForm.contentBackgroundColor,
            bodyBackgrounColor: this.options.editForm.bodyBackgrounColor,
            modalId: 'addManager',
            modal: true,
            beforeClose: function ( ) {
                let tmp = $( '#Zakaz-manager_id' ).val( );
                self._getCustomerContaktList.call( $( '#Zakaz-zak_id' ).next( ).children( 'input:first-child' ), {self: self, afterUpdate: function ( ) {
                        let txt = $( this ).activeDD( 'findByValue', tmp );
                        if ( txt ) {
                            $( this ).val( txt.label );
                            $( '#Zakaz-manager_id' ).val( tmp );
                            $( this ).attr( 'data-key', tmp );
                            self._getCustomerManagerInfo.call( $( this ), {self: self} );
                        }
                    }} );
            },
            requestUpdateParent: function ( tmp ) {
                ////if ( _ZDEBUG ) console.log(tmp);
                //self.update();
            },
            afterInit: function ( dt ) {
                //if ( _ZDEBUG ) console.log( this );
                //if ( _ZDEBUG ) console.log( 'afterInit', $( '#addManager' ).find( '.m_modal-body' ) );
                $( '#addManager' ).parent( ).multiTabController( {
                    requestUrl: self.options.editForm.ajaxlist_zakone,
                    headerText: 'Заказчики',
                    pointPicUrl: self.options.editForm.pointPicUrl,
                    loadPicUrl: self.options.editForm.loadPicUrl,
                    contentBackgroundColor: '#c4c7c7',
                    bodyBackgrounColor: '#fff',
                    firm_id: '#curfirm_id',
                    pjaxId: self.options.editForm.pjaxId
                } );
            }
        };
        if ( this.options.editForm.loadPjaxPicUrl )
            options.loadPjaxPicUrl = this.options.editForm.loadPjaxPicUrl
        if ( this.options.editForm.bankSearchUrl )
            options.bankSearchUrl = this.options.editForm.bankSearchUrl;
        $.custom.simpleForm( options );
    },
    _contentDDClick: function ( e, ui, dd ) {
//            //if ( _ZDEBUG ) console.log(this);
        let tmpOld = $( this ).parent( ).prev( ).val( );
        $( this ).parent( ).prev( ).val( $( this ).attr( 'data-key' ) );
        e.self.__validate( $( this ).parent( ).prev( ) ); //_contentDDClick
        if ( tmpOld !== $( this ).parent( ).prev( ).val( ) )
            $( this ).parent( ).prev( ).trigger( 'change' );
        if ( $.isFunction( e.callb ) )
            e.callb.call( this, e, ui, dd );
    },
    _createTabRow: function ( row ) {
//            //if ( _ZDEBUG ) console.log('row',row);
        let rVal = $( '<div>' ).addClass( 'dialog-row' + ( row.center ? ' center-block' : '' ) );
        let self = this;
        let dopOpt = {controlSize: 190, inline: $.type( row.inline ) === 'undefined' ? false : row.inline};
        dopOpt.controlClass = row.controlClass ? row.controlClass : false;
        dopOpt.labelClass = row.labelClass ? row.labelClass : false;
        dopOpt.controlSize = row.controlSize ? row.controlSize : false;
        $.each( row.fields, function ( ind, el ) {
            if ( $.type( el ) === 'string' ) {
                if ( $.isFunction( self[el] ) ) {
                    rVal.append( self[el].call( self ) );
                } else {
                    rVal.append( $( '<p>' ).text( el ) );
                }
            } else {
                if ( el.activeDD ) {
                    el.activeDD.loadPicUrl = self.options.bigLoaderPicUrl ? self.options.bigLoaderPicUrl : null;
                    if ( el.createHiddenInput )
                        el.activeDD.onClickOrChange = [ {self: self, callb: el.activeDD.onClickOrChange}, self._contentDDClick ];
                }
                //                //if ( _ZDEBUG ) console.log(el,$.extend({},dopOpt,el));
                rVal.append( self._createInputControlGroup( $.extend( {}, dopOpt, el ) ) );
            }
        } );
        return rVal;
    },
    _processTab: function ( tab, opt ) {
//            //if ( _ZDEBUG ) console.log('_processTab',opt);
        let self = this;
        tab.empty( );
        if ( $.isArray( opt.rows ) ) {
            $.each( opt.rows, function ( ind, el ) {
                if ( $.type( el ) === 'object' )
                    tab.append( self._createTabRow( el ) );
                else if ( $.isFunction( self[el] ) )
                    self[el].call( self, tab );
            } );
        } else if ( $.isFunction( self[opt.rows] ) ) {
            this.busy++;
            self[opt.rows].call( self, tab );
            this._busyDown( );
        }
    },
    _drawTabs: function ( ) {
        let self = this;
//            //if ( _ZDEBUG ) console.log('_drawTabs',this.tabs);
        $.each( this.tabs, function ( ind, it ) {
//                self.busy++;
            let el = $( '#tab-pane-zakaz-page' + ( !self.options.isDisainer ? ind : ( ind + 5 ) ) );
            if ( el.length && this.rows )
                self._processTab( el, it );
//                self._busyDown();
        } );
    },
    _drawContent: function ( ) {
        let rVal = $( '<div>' ).addClass( 'tab-content' );
        let menu = $( '<ul>' ).addClass( 'nav nav-tabs' ), self = this;
//            let img=$('<img>').attr({src:this.options.bigLoaderPicUrl});
        let active = false;
        $.each( this.tabs, function ( ind, el ) {
            menu.append( $( '<li>' ).append( $( '<a>' ).attr( {
                'data-toggle': 'tab',
                href: '#tab-pane-zakaz-page' + ( !self.options.isDisainer ? ind : ( ind + 5 ) )
            } ).text( el.label ) ).addClass( !active ? 'active' : '' ) );
            rVal.append( $( '<div>' )
                    .addClass( 'tab-pane' + ( !active ? ' active' : '' ) )
                    .attr( {id: 'tab-pane-zakaz-page' + ( !self.options.isDisainer ? ind : ( ind + 5 ) )} )
                    .append( $( '<p>' ).addClass( 'mid-text' ).text( 'Не готов' ) ) );
            active = true;
        } );
        return $( '<div>' ).addClass( 'main-content' ).append( menu ).append( rVal );
    },
    _reDraw: function ( ) {
        this.options.tabIndex = this.startTabIndex;
//            this.busy++;
        this._reDrawTitle( );
        let form = $( '<form>' )
                .attr( 'id', 'form-' + this.options.form.name )
                .append( $( '<input>' ).attr( {
                    type: 'hidden',
                    name: this._createFieldName( 'tmpName' ),
                    id: 'zakaz-tmpname'
                } ).val( this.options.form.fields.tmpName ? this.options.form.fields.tmpName : null ) )
                .append( this._createSubTitle( ) )
                .append( this._drawContent( ) );
        this.element
                .empty( )
                .append( form );
        this._drawTabs( );
        if ( this.options.isDisainer ) {
            form.append( this._createInput( 'date_of_receipt', {type: 'hidden'} ).val( this.options.form.fields.date_of_receipt ) );
            form.append( this._createInput( 'date_of_receipt1', {type: 'hidden'} ).val( this.options.form.fields.date_of_receipt1 ? this.options.form.fields.date_of_receipt1 : null ) );
        }

//            this._busyDown();
    },
    _recalculateAll: function ( e ) {
        e.data.self.__recalculateAll( );
    },
    __recalculateAll: function ( callRecalculateMaterial ) {
        if ( $( '[name^="Zakaz[materials]"]' ).length === 0 && ( $.type( this.options.form.fields.materials ) != 'array' || this.options.form.fields.materials.length === 0 ) ) {
            $( '#zakaz_material_coast' ).val( 0 );
            $( '#zakaz_material_coast_comerc' ).val( 0 );
        }
        let tCoast = 0,
                tPayment = 0, //Исполнители
                oldTPayment = parseInt( $( '#Zakaz-spending' ).val( ) ), //Исполнители старое значение
                mPayment = parseInt( $( '#zakaz_material_coast' ).val( ) ), //Материалы
                matTCoast = parseInt( $( '#zakaz_material_coast_comerc' ).val( ) ), //Комерческая стоимость материалов
                oldTotalCoast = parseInt( $( '#Zakaz-total_coast' ).val( ) ); //Старая общая стоимость
        //tPayment=!isNaN(tPayment) ?tPayment:0;
        if ( callRecalculateMaterial === true ) {
            this._zakaz_material_recalulate( false );
        }

        if ( callRecalculateMaterial !== false && !this.busy ) {
            this.__recalculateExecutersOther( false );
        }
        oldTPayment = !isNaN( oldTPayment ) ? oldTPayment : 0;
        mPayment = !isNaN( mPayment ) ? mPayment : 0;
        matTCoast = !isNaN( matTCoast ) ? matTCoast : 0;
        let oldmPayment = mPayment;
        oldTotalCoast = !isNaN( oldTotalCoast ) ? oldTotalCoast : 0;
        $( '#Zakaz-spending2' ).val( mPayment );
        $.each( $( '#executers_table' ).find( '[name*=payment]' ), function ( id, el ) {
            let tP = 0, tC = 0;
            if ( $.isNumeric( $( el ).val( ) ) )
                tP = parseInt( $( el ).val( ) );
            let coast = $( el ).parent( ).parent( ).prev( ).find( '[name*=coast]' );
            if ( coast.length )
                if ( $.isNumeric( coast.val( ) ) )
                    tC = parseInt( coast.val( ) );
            tCoast += tC;
            tPayment += tP;
            $( el ).parent( ).parent( ).next( ).find( 'p' ).text( tC - tP );
        } );
        //if ( _ZDEBUG ) console.log( $( '#tab-pane-zakaz-page1' ).find( '[role=calculate]' ).find( '[name*=summ]:not(.off)' ) );
        $.each( $( '#tab-pane-zakaz-page1' ).find( '[role=calculate]' ).find( '[name*=summ]:not(.off)' ), function ( ) {
            let tmp = parseFloat( $( this ).val( ) );
            tmp = !isNaN( tmp ) ? tmp : 0;
            tCoast += tmp;
        } );
        $.each( $( '#tab-pane-zakaz-page1' ).find( '[role=calculate]' ).find( '[name*=payment]:not(.off)' ), function ( ) {
            let tmp = parseFloat( $( this ).val( ) );
            tmp = !isNaN( tmp ) ? tmp : 0;
            tPayment += tmp;
        } );
        tCoast = ( matTCoast + tCoast ); //oldTotalCoast>(matTCoast+tCoast)?oldTotalCoast:(matTCoast+tCoast);
        $( '#Zakaz-total_coast' ).val( tCoast );
        $( '#Zakaz-spending' ).val( tPayment );
        $( '#Zakaz-spending2' ).val( mPayment );
        $( '#Zakaz-profit' ).val( tCoast - tPayment - mPayment );
        if ( tCoast - tPayment - mPayment <= 0 )
            $( '#Zakaz-profit' ).addClass( 'bg-danger' );
        else
            $( '#Zakaz-profit' ).removeClass( 'bg-danger' );
    },
    _isExpress: function ( ) {
        let rV = $( '<div>' ).addClass( 'dialog-control-group re_print' );
        rV.append( this._createInput( 'is_express', {type: 'hidden'} ).val( this.options.form.fields.is_express ? 1 : 0 ) );
        rV.append( $( '<a>' ).text( '' ).addClass( 'btn btn-srochno' + ( this.options.form.fields.is_express ? ' btn-srochno-danger' : '' ) ).click( {self: this}, function ( e ) {
            if ( $( this ).hasClass( 'btn-srochno-danger' ) ) {
                $( this ).removeClass( 'btn-srochno-danger' );
                $( '#Zakaz-is_express' ).val( 0 );
                e.data.self.options.form.fields.is_express = 0;
            } else {
                $( this ).addClass( 'btn-srochno-danger' );
                $( '#Zakaz-is_express' ).val( 1 );
                e.data.self.options.form.fields.is_express = 1;
            }
        } ) );
        return rV;
    },
    _rePrint: function ( ) {
        if ( !this.options.form.fields.re_print ) {
            return null;
        }
        let rV = $( '<div>' ).addClass( 'dialog-control-group re_print' );
        rV.append( this._createInput( 're_print', {type: 'hidden'} ).val( this.options.form.fields.re_print ) );
        rV.append( $( '<a>' ).text( 'Перепечатка' + ( this.options.form.fields.re_print ? ( ' №' + this.options.form.fields.re_print ) : '' ) ).addClass( 'btn ' + ( this.options.form.fields.re_print ? 'btn-danger' : 'btn-default' ) ).click( {self: this}, function ( e ) {
        } ) );
        return rV;
    },
    _toFixed: function ( val ) {
        if ( !val )
            return '0';
        return val.toFixed( Math.round( val * 100 % 100 ) < 10 ? ( Math.round( val * 100 % 100 ) > 0 ? 1 : 0 ) : 2 )
    },
    _totalCount: function ( dopId ) {
        let tmp, tmp_cnt;
        dopId = $.type( dopId ) !== 'undefined' ? dopId : '';
        tmp = $( '#Zakaz-number_of_copies' + dopId ).val( ) ? $( '#Zakaz-number_of_copies' + dopId ).val( ) : '';
        let pos = tmp.indexOf( '*' ), v1, v2;
        if ( pos > -1 ) {
            v1 = parseInt( tmp.substr( 0, pos ) );
            v2 = parseInt( tmp.substr( pos + 1 ) );
            tmp_cnt= !isNaN( v1 ) && !isNaN( v2 ) ? v1 * v2 : 0;
        } else {
            v1 = parseInt( tmp );
            tmp_cnt = !isNaN( v1 ) ? v1 : 0;
        }
        return tmp_cnt;
    },
    _blockProceed: function ( dopId, value ) {
        dopId = $.type( dopId ) !== 'undefined' ? dopId : '';
        if ( $.type( value ) === 'undefined' ) {
            let rVal = $( '#Zakaz-material_block_format' + dopId ).val( ) ? ( JSON.parse( $( '#Zakaz-material_block_format' + dopId ).val( ) ) ) : {block: {}};
            if ( $.type( rVal.block ) === 'undefined' )
                rVal.block = {};
            return rVal;
        } else {
            $( '#Zakaz-material_block_format' + dopId ).val( JSON.stringify( value ) );
            return value;
        }
    },
    _addToRemove: function ( name, id ) {
        let els = $( '#form-Zakaz' ).children( '[name*="' + name + '_to_erase"]' );
        let ind = 0;
        //if ( _ZDEBUG ) console.log( els );
        if ( els.length )
            ind = els.length;
        let inp = $( '<input>' ).attr( {
            type: 'hidden',
            name: 'Zakaz[' + name + '_to_erase][' + ind + ']',
        } ).val( id );
        $( '#form-Zakaz' ).append( inp );
    },
};
$.fn.includeJS( [
    'js/zakaz-executers.js',
    'js/zakaz-material.js',
    'js/zakaz-material-block0.js',
    'js/zakaz-material-block1.js',
    'js/zakaz-material-block2.js',
    'js/zakaz-material-block4.js',
    'js/zakaz-material-block10.js',
    'js/zakaz-material-block11.js',
    'js/zakaz-params.js',
    'js/zakaz-file.js',
    'js/zakaz-postprint.js',
], function ( ) {
    ( function ( $ ) {
        $.widget( "custom.zakazAddEditController", $.custom.maindialog, $.extend( {}, zakaz_controller, zakaz_material, block_constuktor, block_visitki, block_block, zakaz_executers, zakaz_params, zakaz_file, block_paket, zakaz_postprint, block_paket_p, block_suvenirka ) );
    }( jQuery ) );
} );


