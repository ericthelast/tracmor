<?php
/*
 * Copyright (c)  2009, Tracmor, LLC 
 *
 * This file is part of Tracmor.  
 *
 * Tracmor is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version. 
 *	
 * Tracmor is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tracmor; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

	require_once('../includes/prepend.inc.php');
	QApplication::Authenticate(2);
	require_once(__FORMBASE_CLASSES__ . '/AssetListFormBase.class.php');
	require('../assets/AssetMassEditPanel.class.php');
	/**
	 * This is a quick-and-dirty draft form object to do the List All functionality
	 * of the Asset class.  It extends from the code-generated
	 * abstract AssetListFormBase class.
	 *
	 * Any display custimizations and presentation-tier logic can be implemented
	 * here by overriding existing or implementing new methods, properties and variables.
	 *
	 * Additional qform control objects can also be defined and used here, as well.
	 * 
	 * @package Application
	 * @subpackage FormDraftObjects
	 * 
	 */
	class AssetListForm extends AssetListFormBase {
		/**
		 * @var  QLabel     $lblWarning
		 * @var  QDialogBox $dlgMassEdit
		 * @var  QDialogBox $dlgMassDelete
		 * @var  QButton    $btnMassDelete
		 * @var  QButton    $btnMassEdit
		 */

		// Header Tabs
		protected $ctlHeaderMenu;
		
		// Shortcut Menu
		protected $ctlShortcutMenu;
		
		// Search Menu
		public $ctlSearchMenu;
		
		public $arrToEdit;
		protected $pnlAssetMassEdit;
		protected $lblWarning;
		protected $dlgMassEdit;
		protected $dlgMassDelete;
		protected $btnMassEdit;
		protected $btnMassDelete;
		protected $btnMassDeleteConfirm;
		protected $btnMassDeleteCancel;

		protected function Form_Create() {
			
			$this->ctlHeaderMenu_Create();
			$this->ctlShortcutMenu_Create();
			$this->ctlSearchMenu_Create();

			// Mass Actions controls create
			$this->lblWarning_Create();
			$this->dlgMassEdit_Create();
			$this->dlgMassDelete_Create();
			$this->btnMassDelete_Create();
			$this->btnMassDeleteConfirm_Create();
			$this->btnMassDeleteCancel_Create();
			$this->btnMassEdit_Create();
			$this->AddMassButtonActions();
		}

		// Create and Setup the Header Composite Control
		protected function ctlHeaderMenu_Create() {
			$this->ctlHeaderMenu = new QHeaderMenu($this);
		}

		// Create and Setp the Shortcut Menu Composite Control
		protected function ctlShortcutMenu_Create() {
			$this->ctlShortcutMenu = new QShortcutMenu($this);
		}

		// Create and Setup the Asset Search Composite Control
		protected function ctlSearchMenu_Create() {
			$this->ctlSearchMenu = new QAssetSearchComposite($this, null, false);
		}

		// Mass Actions controls creating/handling functions
		protected function dlgMassDelete_Create(){
			$this->dlgMassDelete = new QDialogBox($this);
			$this->dlgMassDelete->AutoRenderChildren = true;
			$this->dlgMassDelete->Width = '440px';
			$this->dlgMassDelete->Overflow = QOverflow::Auto;
			$this->dlgMassDelete->Padding = '10px';
			$this->dlgMassDelete->Display = false;
			$this->dlgMassDelete->BackColor = '#FFFFFF';
			$this->dlgMassDelete->MatteClickable = false;
			$this->dlgMassDelete->CssClass = "modal_dialog";
		}

		protected function dlgMassEdit_Create(){
			$this->dlgMassEdit = new QDialogBox($this, 'MassEdit');
			$this->dlgMassEdit->AutoRenderChildren = true;
			$this->dlgMassEdit->Width = '440px';
			$this->dlgMassEdit->Overflow = QOverflow::Auto;
			$this->dlgMassEdit->Padding = '10px';
			$this->dlgMassEdit->Display = false;
			$this->dlgMassEdit->BackColor = '#FFFFFF';
			$this->dlgMassEdit->MatteClickable = false;
			$this->dlgMassEdit->CssClass = "modal_dialog";
		}

		protected function btnMassDelete_Create(){
			$this->btnMassDelete = new QButton($this);
			$this->btnMassDelete->Name = "delete";
			$this->btnMassDelete->Text = "Mass Delete";
			// Actions added in AddMassButtonActions()
		}

		protected function btnMassDeleteConfirm_Create() {
			$this->btnMassDeleteConfirm = new QButton($this->dlgMassDelete);
			$this->btnMassDeleteConfirm->Text = 'Confirm';
			$this->btnMassDeleteConfirm->AddAction(new QClickEvent(), new QAjaxAction('btnMassDeleteConfirm_Click'));
		}

		protected function btnMassDeleteCancel_Create() {
			$this->btnMassDeleteCancel = new QButton($this->dlgMassDelete);
			$this->btnMassDeleteCancel->Text = 'Cancel';
			$this->btnMassDeleteCancel->SetCustomStyle('margin-left', '4px');
			$this->btnMassDeleteCancel->AddAction(new QClickEvent(), new QHideDialogBox($this->dlgMassDelete));
		}

		protected function btnMassDelete_Click() {
			$items = $this->ctlSearchMenu->dtgAsset->getSelected('AssetId');
			if (count($items)>0) {
				$this->dlgMassDelete->Text = sprintf('<div class="title"> Asset Mass Delete - %s records</div><hr/>', count($items));
				$this->dlgMassDelete->Text .= 'Are you sure you want to DELETE these assets?<br/><br/>';
				$this->dlgMassDelete->ShowDialogBox();
			} else {
				$this->lblWarning->Text = "You must select one or more assets to Delete";
			}
		}

		protected function btnMassEdit_Create(){
			$this->btnMassEdit = new QButton($this);
			$this->btnMassEdit->Name = "edit";
			$this->btnMassEdit->Text = "Mass Edit";
			// Actions added in AddMassButtonActions()
		}

		protected function AddMassButtonActions() {
			// btnMassDelete actions
			$this->btnMassDelete->AddAction(new QClickEvent(), new QAjaxAction('btnMassDelete_Click', null, null, array($this->btnMassEdit, $this->btnMassDelete)));
			$this->btnMassDelete->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnMassDelete_Click', null, null, array($this->btnMassEdit, $this->btnMassDelete)));
			$this->btnMassDelete->AddAction(new QEnterKeyEvent(), new QTerminateAction());

			// btnMassEdit actions
			$this->btnMassEdit->AddAction(new QClickEvent(), new  QAjaxAction('btnMassEdit_Click', null, null, array($this->btnMassEdit, $this->btnMassDelete)));
			$this->btnMassEdit->AddAction(new QEnterKeyEvent(), new QAjaxAction('btnMassEdit_Click', null, null, array($this->btnMassEdit, $this->btnMassDelete)));
			$this->btnMassEdit->AddAction(new QEnterKeyEvent(), new QTerminateAction());
		}

		protected function lblWarning_Create(){
			$this->lblWarning = new QLabel($this);
			$this->lblWarning->Text = "";
			$this->lblWarning->CssClass = "warning";
		}

		protected function btnMassDeleteConfirm_Click(){
			$items = $this->ctlSearchMenu->dtgAsset->getSelected('AssetId');

			$this->lblWarning->Text = "";
			// TODO perform validate
			foreach($items as $item){
				try {
					// Get an instance of the database
					$objDatabase = QApplication::$Database[1];
					// Begin a MySQL Transaction to be either committed or rolled back
					$objDatabase->TransactionBegin();
					// ParentAssetId Field must be manually deleted because MySQL ON DELETE will not cascade to them
					Asset::ResetParentAssetIdToNullByAssetId($item);
					// Delete any audit scans of this asset
					Asset::DeleteAuditScanByAssetId($item);
					// Delete the asset
					Asset::LoadByAssetId($item)->Delete();
					$objDatabase->TransactionCommit();

					// Hide the dialog
					$this->dlgMassDelete->HideDialogBox();
				} catch (QMySqliDatabaseException $objExc) {
					// Rollback the transaction
					$objDatabase->TransactionRollback();
					throw new QDatabaseException();
				}
			}
		}

		protected function btnMassEdit_Click() {
			$this->arrToEdit = $this->ctlSearchMenu->dtgAsset->getSelected('AssetId');
			if (count($this->arrToEdit)>0) {
				$this->lblWarning->Text = "";
				$this->dlgMassEdit->RemoveChildControls(true);
				$this->pnlAssetMassEdit = new AssetMassEditPanel($this->dlgMassEdit, 'pnlAssetMassEditCancel_Click', $this->arrToEdit);
				$this->dlgMassEdit->ShowDialogBox();
			} else {
				$this->lblWarning->Text = "You must select one or more assets to Edit" ;
			}
		}

		public function pnlAssetMassEditCancel_Click() {
			$this->dlgMassEdit->HideDialogBox();
		}

		public function UncheckAllItems($object) {
			foreach ($object->GetAllControls() as $objControl) {
				if (substr($objControl->ControlId, 0, 11) == 'chkSelected') {
					$objControl->Checked = false;
				}
			}
		}
	}

	// Go ahead and run this form object to generate the page and event handlers, using
	// generated/asset_edit.php.inc as the included HTML template file
	// AssetListForm::Run('AssetListForm', './Qcodo/assets/asset_list.php.inc');
	AssetListForm::Run('AssetListForm', __DOCROOT__ . __SUBDIRECTORY__ . '/assets/asset_list.tpl.php');
?>