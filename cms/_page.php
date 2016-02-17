<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                   ATTENTION!
 * If you see this message in your browser (Internet Explorer, Mozilla Firefox, Google Chrome, etc.)
 * this means that PHP is not properly installed on your web server. Please refer to the PHP manual
 * for more details: http://php.net/manual/install.php 
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */


    include_once dirname(__FILE__) . '/' . 'components/utils/check_utils.php';
    CheckPHPVersion();
    CheckTemplatesCacheFolderIsExistsAndWritable();


    include_once dirname(__FILE__) . '/' . 'phpgen_settings.php';
    include_once dirname(__FILE__) . '/' . 'database_engine/mysql_engine.php';
    include_once dirname(__FILE__) . '/' . 'components/page.php';
    include_once dirname(__FILE__) . '/' . 'authorization.php';

    function GetConnectionOptions()
    {
        $result = GetGlobalConnectionOptions();
        $result['client_encoding'] = 'utf8';
        GetApplication()->GetUserAuthorizationStrategy()->ApplyIdentityToConnectionOptions($result);
        return $result;
    }

    
    // OnGlobalBeforePageExecute event handler
    
    
    // OnBeforePageExecute event handler
    
    
    
    class _pagePage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->dataset = new TableDataset(
                new MySqlIConnectionFactory(),
                GetConnectionOptions(),
                '`_page`');
            $field = new IntegerField('id', null, null, true);
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, true);
            $field = new IntegerField('page_id');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new StringField('type');
            $this->dataset->AddField($field, false);
            $field = new StringField('wireframe');
            $this->dataset->AddField($field, false);
            $field = new StringField('title');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new StringField('picture');
            $this->dataset->AddField($field, false);
            $field = new StringField('lead');
            $this->dataset->AddField($field, false);
            $field = new StringField('page_description');
            $this->dataset->AddField($field, false);
            $field = new StringField('page_keywords');
            $this->dataset->AddField($field, false);
            $field = new StringField('url_external');
            $this->dataset->AddField($field, false);
            $field = new IntegerField('level_down');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new IntegerField('restricted');
            $this->dataset->AddField($field, false);
            $field = new StringField('extra');
            $this->dataset->AddField($field, false);
            $field = new StringField('class');
            $this->dataset->AddField($field, false);
            $field = new IntegerField('hidden');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new IntegerField('ord');
            $field->SetIsNotNull(true);
            $this->dataset->AddField($field, false);
            $field = new IntegerField('new_menu');
            $this->dataset->AddField($field, false);
            $this->dataset->AddCustomCondition(EnvVariablesUtils::EvaluateVariableTemplate($this->GetColumnVariableContainer(), '_page.page_id = 0'));
        }
    
        protected function DoPrepare() {
    
        }
    
        protected function CreatePageNavigator()
        {
            $result = new CompositePageNavigator($this);
            
            $partitionNavigator = new PageNavigator('pnav', $this, $this->dataset);
            $partitionNavigator->SetRowsPerPage(30);
            $result->AddPageNavigator($partitionNavigator);
            
            return $result;
        }
    
        public function GetPageList()
        {
            $currentPageCaption = $this->GetShortCaption();
            $result = new PageList($this);
            $result->AddGroup($this->RenderText('<i class="kre3m kre3m-pages"></i>Vsebina'));
            $result->AddGroup($this->RenderText('<i class="kre3m kre3m-settings"></i>Nastavitve'));
            if (GetCurrentUserGrantForDataSource('_page')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Strani'), '_page.php', $this->RenderText('Strani'), $currentPageCaption == $this->RenderText('Strani'), false, $this->RenderText('<i class="kre3m kre3m-pages"></i>Vsebina')));
            if (GetCurrentUserGrantForDataSource('_content')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Vsebina'), '_content.php', $this->RenderText('Vsebina'), $currentPageCaption == $this->RenderText('Vsebina'), false, $this->RenderText('<i class="kre3m kre3m-pages"></i>Vsebina')));
            if (GetCurrentUserGrantForDataSource('_locale')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Nastavitve'), '_locale.php', $this->RenderText('Nastavitve'), $currentPageCaption == $this->RenderText('Nastavitve'), false, $this->RenderText('<i class="kre3m kre3m-settings"></i>Nastavitve')));
            if (GetCurrentUserGrantForDataSource('project')->HasViewGrant())
                $result->AddPage(new PageLink($this->RenderText('Projekti'), 'project.php', $this->RenderText('Projekti'), $currentPageCaption == $this->RenderText('Projekti'), false, $this->RenderText('<i class="kre3m kre3m-pages"></i>Vsebina')));
            
            if ( HasAdminPage() && GetApplication()->HasAdminGrantForCurrentUser() ) {
              $result->AddGroup('Admin area');
              $result->AddPage(new PageLink($this->GetLocalizerCaptions()->GetMessageString('AdminPage'), 'phpgen_admin.php', $this->GetLocalizerCaptions()->GetMessageString('AdminPage'), false, false, 'Admin area'));
            }
            return $result;
        }
    
        protected function CreateRssGenerator()
        {
            return null;
        }
    
        protected function CreateGridSearchControl(Grid $grid)
        {
            $grid->UseFilter = true;
            $grid->SearchControl = new SimpleSearch('_pagessearch', $this->dataset,
                array('id', 'type', 'wireframe', 'title', 'picture', 'page_description', 'page_keywords', 'url_external', 'hidden', 'ord'),
                array($this->RenderText('Id'), $this->RenderText('Tip'), $this->RenderText('Postavitev'), $this->RenderText('Naziv'), $this->RenderText('Slika'), $this->RenderText('SEO opis'), $this->RenderText('SEO kljuène besede'), $this->RenderText('Zunanja povezava'), $this->RenderText('Skrito'), $this->RenderText('Vrstni red')),
                array(
                    '=' => $this->GetLocalizerCaptions()->GetMessageString('equals'),
                    '<>' => $this->GetLocalizerCaptions()->GetMessageString('doesNotEquals'),
                    '<' => $this->GetLocalizerCaptions()->GetMessageString('isLessThan'),
                    '<=' => $this->GetLocalizerCaptions()->GetMessageString('isLessThanOrEqualsTo'),
                    '>' => $this->GetLocalizerCaptions()->GetMessageString('isGreaterThan'),
                    '>=' => $this->GetLocalizerCaptions()->GetMessageString('isGreaterThanOrEqualsTo'),
                    'ILIKE' => $this->GetLocalizerCaptions()->GetMessageString('Like'),
                    'STARTS' => $this->GetLocalizerCaptions()->GetMessageString('StartsWith'),
                    'ENDS' => $this->GetLocalizerCaptions()->GetMessageString('EndsWith'),
                    'CONTAINS' => $this->GetLocalizerCaptions()->GetMessageString('Contains')
                    ), $this->GetLocalizerCaptions(), $this, 'CONTAINS'
                );
        }
    
        protected function CreateGridAdvancedSearchControl(Grid $grid)
        {
            $this->AdvancedSearchControl = new AdvancedSearchControl('_pageasearch', $this->dataset, $this->GetLocalizerCaptions(), $this->GetColumnVariableContainer(), $this->CreateLinkBuilder());
            $this->AdvancedSearchControl->setTimerInterval(1000);
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('id', $this->RenderText('Id')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('type', $this->RenderText('Tip')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('wireframe', $this->RenderText('Postavitev')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('title', $this->RenderText('Naziv')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('picture', $this->RenderText('Slika')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('page_description', $this->RenderText('SEO opis')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('page_keywords', $this->RenderText('SEO kljuène besede')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('url_external', $this->RenderText('Zunanja povezava')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('hidden', $this->RenderText('Skrito')));
            $this->AdvancedSearchControl->AddSearchColumn($this->AdvancedSearchControl->CreateStringSearchInput('ord', $this->RenderText('Vrstni red')));
        }
    
        protected function AddOperationsColumns(Grid $grid)
        {
            $actionsBandName = 'actions';
            $grid->AddBandToBegin($actionsBandName, $this->GetLocalizerCaptions()->GetMessageString('Actions'), true);
            if ($this->GetSecurityInfo()->HasViewGrant())
            {
                $column = new RowOperationByLinkColumn($this->GetLocalizerCaptions()->GetMessageString('View'), OPERATION_VIEW, $this->dataset);
                $grid->AddViewColumn($column, $actionsBandName);
                $column->SetImagePath('images/view_action.png');
            }
            if ($this->GetSecurityInfo()->HasEditGrant())
            {
                $column = new RowOperationByLinkColumn($this->GetLocalizerCaptions()->GetMessageString('Edit'), OPERATION_EDIT, $this->dataset);
                $grid->AddViewColumn($column, $actionsBandName);
                $column->SetImagePath('images/edit_action.png');
                $column->OnShow->AddListener('ShowEditButtonHandler', $this);
            }
            if ($this->GetSecurityInfo()->HasDeleteGrant())
            {
                $column = new RowOperationByLinkColumn($this->GetLocalizerCaptions()->GetMessageString('Delete'), OPERATION_DELETE, $this->dataset);
                $grid->AddViewColumn($column, $actionsBandName);
                $column->SetImagePath('images/delete_action.png');
                $column->OnShow->AddListener('ShowDeleteButtonHandler', $this);
                $column->SetAdditionalAttribute('data-modal-delete', 'true');
                $column->SetAdditionalAttribute('data-delete-handler-name', $this->GetModalGridDeleteHandler());
            }
        }
    
        protected function AddFieldColumns(Grid $grid)
        {
            //
            // View column for type field
            //
            $column = new TextViewColumn('type', 'Tip', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('title', 'Naziv', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('_pageGrid_title_handler_list');
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for picture field
            //
            $column = new ExternalImageColumn('picture', 'Slika', $this->dataset, '');
            $column->SetSourcePrefix('_custom/timthumb.php?w=100&h=100&q=80&src=../media/uploads/page/');
            $column->SetSourceSuffix('');
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for url_external field
            //
            $column = new TextViewColumn('url_external', 'Zunanja povezava', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('_pageGrid_url_external_handler_list');
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for hidden field
            //
            $column = new TextViewColumn('hidden', 'Skrito', $this->dataset);
            $column->SetOrderable(true);
            $column = new CheckBoxFormatValueViewColumnDecorator($column);
            $column->SetDisplayValues($this->RenderText('<img src="images/checked.png" alt="true">'), $this->RenderText(''));
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for ord field
            //
            $column = new TextViewColumn('ord', 'Vrstni red', $this->dataset);
            $column->SetOrderable(true);
            $column->SetDescription($this->RenderText(''));
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for id field
            //
            $column = new TextViewColumn('id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for type field
            //
            $column = new TextViewColumn('type', 'Tip', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for wireframe field
            //
            $column = new TextViewColumn('wireframe', 'Postavitev', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('title', 'Naziv', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('_pageGrid_title_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for picture field
            //
            $column = new ExternalImageColumn('picture', 'Slika', $this->dataset, '');
            $column->SetSourcePrefix('_custom/timthumb.php?w=100&h=100&q=80&src=../media/uploads/page/');
            $column->SetSourceSuffix('');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for page_description field
            //
            $column = new TextViewColumn('page_description', 'SEO opis', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('_pageGrid_page_description_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for page_keywords field
            //
            $column = new TextViewColumn('page_keywords', 'SEO kljuène besede', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('_pageGrid_page_keywords_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for url_external field
            //
            $column = new TextViewColumn('url_external', 'Zunanja povezava', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('_pageGrid_url_external_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for hidden field
            //
            $column = new TextViewColumn('hidden', 'Skrito', $this->dataset);
            $column->SetOrderable(true);
            $column = new CheckBoxFormatValueViewColumnDecorator($column);
            $column->SetDisplayValues($this->RenderText('<img src="images/checked.png" alt="true">'), $this->RenderText(''));
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for ord field
            //
            $column = new TextViewColumn('ord', 'Vrstni red', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for type field
            //
            $editor = new ComboBox('type_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editor->AddValue('main', $this->RenderText('glavni'));
            $editor->AddValue('hidden', $this->RenderText('skrito'));
            $editor->AddValue('top', $this->RenderText('vrhnji'));
            $editor->AddValue('footer', $this->RenderText('noga'));
            $editColumn = new CustomEditColumn('Tip', 'type', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for wireframe field
            //
            $editor = new ComboBox('wireframe_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editor->AddValue('index', $this->RenderText('privzeta'));
            $editor->AddValue('home', $this->RenderText('vstopna'));
            $editor->AddValue('sitemap', $this->RenderText('sitemap'));
            $editColumn = new CustomEditColumn('Postavitev', 'wireframe', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for title field
            //
            $editor = new TextEdit('title_edit');
            $editor->SetSize(75);
            $editor->SetMaxLength(255);
            $editColumn = new CustomEditColumn('Naziv', 'title', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for picture field
            //
            $editor = new ImageUploader('picture_edit');
            $editor->SetShowImage(true);
            $editColumn = new UploadFileToFolderColumn('Slika', 'picture', $editor, $this->dataset, false, false, '../media/uploads/page/');
            $editColumn->OnCustomFileName->AddListener('picture_GenerateFileName_edit', $this);
            $editColumn->SetReplaceUploadedFileIfExist(true);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for page_description field
            //
            $editor = new TextEdit('page_description_edit');
            $editor->SetSize(75);
            $editor->SetMaxLength(255);
            $editColumn = new CustomEditColumn('SEO opis', 'page_description', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for page_keywords field
            //
            $editor = new TextEdit('page_keywords_edit');
            $editor->SetSize(75);
            $editor->SetMaxLength(255);
            $editColumn = new CustomEditColumn('SEO kljuène besede', 'page_keywords', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for url_external field
            //
            $editor = new TextEdit('url_external_edit');
            $editor->SetSize(75);
            $editor->SetMaxLength(255);
            $editColumn = new CustomEditColumn('Zunanja povezava', 'url_external', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for hidden field
            //
            $editor = new CheckBox('hidden_edit');
            $editColumn = new CustomEditColumn('Skrito', 'hidden', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for ord field
            //
            $editor = new SpinEdit('ord_edit');
            $editor->SetUseConstraints(true);
            $editor->SetMaxValue(1000);
            $editor->SetMinValue(0);
            $editor->SetStep(10);
            $editColumn = new CustomEditColumn('Vrstni red', 'ord', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for type field
            //
            $editor = new ComboBox('type_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editor->AddValue('main', $this->RenderText('glavni'));
            $editor->AddValue('hidden', $this->RenderText('skrito'));
            $editor->AddValue('top', $this->RenderText('vrhnji'));
            $editor->AddValue('footer', $this->RenderText('noga'));
            $editColumn = new CustomEditColumn('Tip', 'type', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $editColumn->SetAllowSetToDefault(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for wireframe field
            //
            $editor = new ComboBox('wireframe_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $editor->AddValue('index', $this->RenderText('privzeta'));
            $editor->AddValue('home', $this->RenderText('vstopna'));
            $editor->AddValue('sitemap', $this->RenderText('sitemap'));
            $editColumn = new CustomEditColumn('Postavitev', 'wireframe', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $editColumn->SetAllowSetToDefault(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for title field
            //
            $editor = new TextEdit('title_edit');
            $editor->SetSize(75);
            $editor->SetMaxLength(255);
            $editColumn = new CustomEditColumn('Naziv', 'title', $editor, $this->dataset);
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for picture field
            //
            $editor = new ImageUploader('picture_edit');
            $editor->SetShowImage(true);
            $editColumn = new UploadFileToFolderColumn('Slika', 'picture', $editor, $this->dataset, false, false, '../media/uploads/page/');
            $editColumn->OnCustomFileName->AddListener('picture_GenerateFileName_insert', $this);
            $editColumn->SetReplaceUploadedFileIfExist(true);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for page_description field
            //
            $editor = new TextEdit('page_description_edit');
            $editor->SetSize(75);
            $editor->SetMaxLength(255);
            $editColumn = new CustomEditColumn('SEO opis', 'page_description', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for page_keywords field
            //
            $editor = new TextEdit('page_keywords_edit');
            $editor->SetSize(75);
            $editor->SetMaxLength(255);
            $editColumn = new CustomEditColumn('SEO kljuène besede', 'page_keywords', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for url_external field
            //
            $editor = new TextEdit('url_external_edit');
            $editor->SetSize(75);
            $editor->SetMaxLength(255);
            $editColumn = new CustomEditColumn('Zunanja povezava', 'url_external', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for hidden field
            //
            $editor = new CheckBox('hidden_edit');
            $editColumn = new CustomEditColumn('Skrito', 'hidden', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $editColumn->SetAllowSetToDefault(true);
            $editColumn->SetInsertDefaultValue($this->RenderText('0'));
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for ord field
            //
            $editor = new SpinEdit('ord_edit');
            $editor->SetUseConstraints(true);
            $editor->SetMaxValue(1000);
            $editor->SetMinValue(0);
            $editor->SetStep(10);
            $editColumn = new CustomEditColumn('Vrstni red', 'ord', $editor, $this->dataset);
            $editColumn->SetAllowSetToDefault(true);
            $editColumn->SetInsertDefaultValue($this->RenderText('0'));
            $validator = new RequiredValidator(StringUtils::Format($this->GetLocalizerCaptions()->GetMessageString('RequiredValidationMessage'), $this->RenderText($editColumn->GetCaption())));
            $editor->GetValidatorCollection()->AddValidator($validator);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            if ($this->GetSecurityInfo()->HasAddGrant())
            {
                $grid->SetShowAddButton(true);
                $grid->SetShowInlineAddButton(false);
            }
            else
            {
                $grid->SetShowInlineAddButton(false);
                $grid->SetShowAddButton(false);
            }
        }
    
        protected function AddPrintColumns(Grid $grid)
        {
            //
            // View column for id field
            //
            $column = new TextViewColumn('id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for type field
            //
            $column = new TextViewColumn('type', 'Tip', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for wireframe field
            //
            $column = new TextViewColumn('wireframe', 'Postavitev', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('title', 'Title', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for picture field
            //
            $column = new TextViewColumn('picture', 'Picture', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for page_description field
            //
            $column = new TextViewColumn('page_description', 'Page Description', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for page_keywords field
            //
            $column = new TextViewColumn('page_keywords', 'Page Keywords', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for url_external field
            //
            $column = new TextViewColumn('url_external', 'Url External', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for hidden field
            //
            $column = new TextViewColumn('hidden', 'Skrito', $this->dataset);
            $column->SetOrderable(true);
            $column = new CheckBoxFormatValueViewColumnDecorator($column);
            $column->SetDisplayValues($this->RenderText('<img src="images/checked.png" alt="true">'), $this->RenderText(''));
            $grid->AddPrintColumn($column);
            
            //
            // View column for ord field
            //
            $column = new TextViewColumn('ord', 'Vrstni red', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for id field
            //
            $column = new TextViewColumn('id', 'Id', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for type field
            //
            $column = new TextViewColumn('type', 'Tip', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for wireframe field
            //
            $column = new TextViewColumn('wireframe', 'Postavitev', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for title field
            //
            $column = new TextViewColumn('title', 'Title', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for picture field
            //
            $column = new TextViewColumn('picture', 'Picture', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for page_description field
            //
            $column = new TextViewColumn('page_description', 'Page Description', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for page_keywords field
            //
            $column = new TextViewColumn('page_keywords', 'Page Keywords', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for url_external field
            //
            $column = new TextViewColumn('url_external', 'Url External', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for hidden field
            //
            $column = new TextViewColumn('hidden', 'Skrito', $this->dataset);
            $column->SetOrderable(true);
            $column = new CheckBoxFormatValueViewColumnDecorator($column);
            $column->SetDisplayValues($this->RenderText('<img src="images/checked.png" alt="true">'), $this->RenderText(''));
            $grid->AddExportColumn($column);
            
            //
            // View column for ord field
            //
            $column = new TextViewColumn('ord', 'Vrstni red', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
        }
    
        public function GetPageDirection()
        {
            return null;
        }
    
        protected function ApplyCommonColumnEditProperties(CustomEditColumn $column)
        {
            $column->SetDisplaySetToNullCheckBox(false);
            $column->SetDisplaySetToDefaultCheckBox(false);
    		$column->SetVariableContainer($this->GetColumnVariableContainer());
        }
    
        function GetCustomClientScript()
        {
            return ;
        }
        
        function GetOnPageLoadedClientScript()
        {
            return ;
        }
        function _pageGrid_BeforeUpdateRecord($page, &$rowData, &$cancel, &$message, $tableName)
        {
            if (!empty ($rowData['picture']) && strpos ($rowData['picture'], '/') !== false) {
                 $rowData['picture'] = substr ($rowData['picture'], strripos ($rowData['picture'], '/') + 1);
                 if (preg_match ('/\d*\.$/is', $rowData['picture'])) $rowData['picture'] = ''; 
            }
        }
        function _pageGrid_BeforeInsertRecord($page, &$rowData, &$cancel, &$message, $tableName)
        {
            if (!empty ($rowData['picture']) && strpos ($rowData['picture'], '/') !== false) {
                 $rowData['picture'] = substr ($rowData['picture'], strripos ($rowData['picture'], '/') + 1);
                 if (preg_match ('/\d*\.$/is', $rowData['picture'])) $rowData['picture'] = ''; 
            }
        }
        public function picture_GenerateFileName_edit(&$filepath, &$handled, $original_file_name, $original_file_extension, $file_size)
        {
        $targetFolder = FormatDatasetFieldsTemplate($this->GetDataset(), '../media/uploads/page/');
        FileUtils::ForceDirectories($targetFolder);
        
        $filename = ApplyVarablesMapToTemplate('%original_file_name%',
            array(
                'original_file_name' => $original_file_name,
                'original_file_extension' => $original_file_extension,
                'file_size' => $file_size
            )
        );
        $filepath = Path::Combine($targetFolder, $filename);
        
        $handled = true;
        }
        public function picture_GenerateFileName_insert(&$filepath, &$handled, $original_file_name, $original_file_extension, $file_size)
        {
        $targetFolder = FormatDatasetFieldsTemplate($this->GetDataset(), '../media/uploads/page/');
        FileUtils::ForceDirectories($targetFolder);
        
        $filename = ApplyVarablesMapToTemplate('%original_file_name%',
            array(
                'original_file_name' => $original_file_name,
                'original_file_extension' => $original_file_extension,
                'file_size' => $file_size
            )
        );
        $filepath = Path::Combine($targetFolder, $filename);
        
        $handled = true;
        }
        public function ShowEditButtonHandler(&$show)
        {
            if ($this->GetRecordPermission() != null)
                $show = $this->GetRecordPermission()->HasEditGrant($this->GetDataset());
        }
        public function ShowDeleteButtonHandler(&$show)
        {
            if ($this->GetRecordPermission() != null)
                $show = $this->GetRecordPermission()->HasDeleteGrant($this->GetDataset());
        }
        
        public function GetModalGridDeleteHandler() { return '_page_modal_delete'; }
        protected function GetEnableModalGridDelete() { return true; }
    
        protected function CreateGrid()
        {
            $result = new Grid($this, $this->dataset, '_pageGrid');
            if ($this->GetSecurityInfo()->HasDeleteGrant())
               $result->SetAllowDeleteSelected(false);
            else
               $result->SetAllowDeleteSelected(false);   
            
            ApplyCommonPageSettings($this, $result);
            
            $result->SetUseImagesForActions(true);
            $result->SetEditClientFormLoadedScript($this->RenderText('if ($(\'*[data-toggle-name="picture_edit_action"]\').length > 0) {
                 pic = $(\'*[data-toggle-name="picture_edit_action"]\').prevAll(\'img\');
                 $(pic).css(\'width\', \'100px\');
                 if (!$(pic).attr(\'src\').match(/\/media/)) {
                      $(pic).attr(\'src\', \'_custom/timthumb.php?w=100&h=100&q=80&src=../media/uploads/page/\' + $(pic).attr(\'src\'));
                 }
            }'));
            $result->SetUseFixedHeader(false);
            $result->SetShowLineNumbers(false);
            
            $result->SetHighlightRowAtHover(true);
            $result->SetWidth('');
            $result->BeforeUpdateRecord->AddListener('_pageGrid' . '_' . 'BeforeUpdateRecord', $this);
            $result->BeforeInsertRecord->AddListener('_pageGrid' . '_' . 'BeforeInsertRecord', $this);
            $this->CreateGridSearchControl($result);
            $this->CreateGridAdvancedSearchControl($result);
            $this->AddOperationsColumns($result);
            $this->AddFieldColumns($result);
            $this->AddSingleRecordViewColumns($result);
            $this->AddEditColumns($result);
            $this->AddInsertColumns($result);
            $this->AddPrintColumns($result);
            $this->AddExportColumns($result);
    
            $this->SetShowPageList(true);
            $this->SetHidePageListByDefault(false);
            $this->SetExportToExcelAvailable(false);
            $this->SetExportToWordAvailable(false);
            $this->SetExportToXmlAvailable(false);
            $this->SetExportToCsvAvailable(false);
            $this->SetExportToPdfAvailable(false);
            $this->SetPrinterFriendlyAvailable(false);
            $this->SetSimpleSearchAvailable(true);
            $this->SetAdvancedSearchAvailable(false);
            $this->SetFilterRowAvailable(true);
            $this->SetVisualEffectsEnabled(true);
            $this->SetShowTopPageNavigator(true);
            $this->SetShowBottomPageNavigator(true);
    
            //
            // Http Handlers
            //
            //
            // View column for title field
            //
            $column = new TextViewColumn('title', 'Naziv', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, '_pageGrid_title_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for url_external field
            //
            $column = new TextViewColumn('url_external', 'Zunanja povezava', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, '_pageGrid_url_external_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);//
            // View column for title field
            //
            $column = new TextViewColumn('title', 'Naziv', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, '_pageGrid_title_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for page_description field
            //
            $column = new TextViewColumn('page_description', 'SEO opis', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, '_pageGrid_page_description_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for page_keywords field
            //
            $column = new TextViewColumn('page_keywords', 'SEO kljuène besede', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, '_pageGrid_page_keywords_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            //
            // View column for url_external field
            //
            $column = new TextViewColumn('url_external', 'Zunanja povezava', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, '_pageGrid_url_external_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            return $result;
        }
        
        public function OpenAdvancedSearchByDefault()
        {
            return false;
        }
    
        protected function DoGetGridHeader()
        {
            return '';
        }
    }

    SetUpUserAuthorization(GetApplication());

    try
    {
        $Page = new _pagePage("_page.php", "_page", GetCurrentUserGrantForDataSource("_page"), 'UTF-8');
        $Page->SetShortCaption('Strani');
        $Page->SetHeader(GetPagesHeader());
        $Page->SetFooter(GetPagesFooter());
        $Page->SetCaption('Strani');
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("_page"));
        GetApplication()->SetEnableLessRunTimeCompile(GetEnableLessFilesRunTimeCompilation());
        GetApplication()->SetCanUserChangeOwnPassword(
            !function_exists('CanUserChangeOwnPassword') || CanUserChangeOwnPassword());
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e->getMessage());
    }
	
