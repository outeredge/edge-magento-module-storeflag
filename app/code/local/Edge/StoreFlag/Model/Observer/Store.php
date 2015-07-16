<?php

class Edge_StoreFlag_Model_Observer_Store
{
    public function addFlagField(Varien_Event_Observer $observer)
    {
        if (Mage::registry('store_type') == 'store') {
            $block = $observer->getEvent()->getBlock();

            $form = $block->getForm();
            $form->addData(array(
                'enctype' => 'multipart/form-data'
            ));

            $fieldset = $form->getElement('store_fieldset');

            $storeModel = Mage::registry('store_data');
            $fieldset->addField('flag', 'image', array(
                'label' => Mage::helper('adminhtml')->__('Flag'),
                'name' => 'flag',
                'value' => $storeModel->getFlag(),
                'disabled'  => $storeModel->isReadOnly()
            ));
        }
    }

    public function saveFlag(Varien_Event_Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        $data = Mage::app()->getRequest()->getPost();

        if (!empty($_FILES)) {
            if (isset($_FILES['flag']['name']) && $_FILES['flag']['name'] != '') {
                try {
                    $uploader = new Mage_Core_Model_File_Uploader('flag');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','svg'));
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);

                    $dirPath = Mage::getBaseDir('media') . DS . 'store_flag';
                    $result = $uploader->save($dirPath, $_FILES['flag']['name']);

                } catch (Exception $e) {
                    Mage::log($e->getMessage());
                }

                $store->setFlag('store_flag' . $result['file']);
            }
            elseif (isset($data['flag']) && is_array($data['flag'])) {
                if (isset($data['flag']['delete']) && $data['flag']['delete'] === "1") {
                    $store->setFlag(null);
                } else {
                    $store->setFlag($data['flag']['value']);
                }
            }
        }
    }
}