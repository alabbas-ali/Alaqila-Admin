<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PageController
 *
 * @author abass
 */

namespace Myuploads\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Myuploads\Form\MyuploadForm;
use Zend\View\Model\JsonModel;


class MyuploadController extends AbstractActionController {

    protected $entityManager;

    public function getEntityManager() {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->entityManager;
    }

    public function indexAction() {
        //$form = new UploadForm();
        $folder = $this->zfcUserAuthentication()->getIdentity()->__get('id');
        $form = new MyuploadForm('file-form' , $folder);
        //$tempFile = null;

        $request = $this->getRequest();
        if ($request->isPost()) {
            // Make certain to merge the files info!
            $post = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );

            $form->setData($post);
            if ($form->isValid()) {
                $data = $form->getData();
                // Form is valid, save the form!
                if (!empty($post['isAjax'])) {
                    return new JsonModel(array(
                        'status' => true,
                        'redirect' => $this->redirect()->toRoute('Myuploads'),
                        'formData' => $data,
                    ));
                } else {
                    // Fallback for non-JS clients
                    return $this->redirect()->toRoute('Myuploads');
                }
            } else {
                if (!empty($post['isAjax'])) {
                    // Send back failure information via JSON
                    return new JsonModel(array(
                        'status' => false,
                        'formErrors' => $form->getMessages(),
                        'formData' => $form->getData(),
                    ));
                }
            }
        }
        $return = new ViewModel(array('form' => $form));
        $return->setTerminal(true);
        return $return;
    }

    public function uploadProgressAction() {
        $id = $this->params()->fromQuery('id', null);
        //$progress = new \Zend\ProgressBar\Upload\SessionProgress();
        $progress = new \Zend\ProgressBar\Upload\ApcProgress();
        return new JsonModel($progress->getProgress($id));
    }
    
    public function fileDeleteAction(){
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            //var_dump($post['file']);  die();
            $folder = $this->zfcUserAuthentication()->getIdentity()->__get('id');
            $dir = 'public/uploads/'.$folder.'/';
            $file = $dir.'orginal/'.$post['file'];
            if(file_exists($file)){
                unlink($file);
                return new JsonModel(array("done" => 'true', 'message' => 'News Have been Deleted'));
            }else{
                return new JsonModel(array("done" => 'false', 'message' => 'Sorry The File Is Not Exist in Server'));
            }
        }
        return new JsonModel(array("done" => 'false', 'message' => 'Sorry But Can not Delete This'));
    }
    
    public function filesdisplayAction() {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            $id = 1;
        }
        $folder = $this->zfcUserAuthentication()->getIdentity()->__get('id');
        $dir = 'public/uploads/'.$folder.'/';
        //var_dump($dir); 
        //die();
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true); 
            mkdir($dir."orginal", 0777, true);
            mkdir($dir."300-300", 0777, true); 
            mkdir($dir."350-175", 0777, true);
            mkdir($dir."600-600", 0777, true);
            mkdir($dir."700-350", 0777, true);
        }
        $dir = $dir.'orginal/';
        $files = scandir($dir);
        $x = 0;
        $z = 30;
        $q = ($id - 1) * $z;
        $q1 = $id * $z;
        $filescount = sizeof($files) - 2;
        $filesnum = $filescount - $q1;
        if ($filesnum < 0)
            $filesnum = 0;
        $filelist = array();
        
        $settings = $this->getEntityManager()->find('Settings\Model\Settings', 1);
        $uploudfolder = $settings->upload_folder;
        
        foreach ($files as $file) {
            if ($file === '.' or $file === '..')
                continue;
            if ($x >= $q && $x < $q1) {
                $filelist[] = $uploudfolder . $dir . $file;
            }
            $x++;
        }
        
        $filesview = '';
        foreach ($filelist as $file) {
            $file_ext = pathinfo($file, PATHINFO_EXTENSION);
            if ($file_ext == "jpg" OR $file_ext == "png" OR $file_ext == "gif") {
            $filesview[] = ""
                    . "<div class='col-xs-6 col-sm-4 col-md-3 pull-right content'>"
                    . "<div class='thumbnail'>"
                    . "<div class='deletefile' onclick='deleteFile(\"$file\")'><i class='fa fa-trash-o'></i></div>"
                    . "<div class='centered'>"
                    . "<img class='' onclick='selecteOneFile(this)' src='$file'>"
                    . "</div>"
                    . "</div>"
                    . "</div>";
            }
            
            if ($file_ext == "mp4") {
            $filesview[] = ""
                    . "<div class='col-xs-6 col-sm-4 col-md-3 pull-right content'>"
                    . "<div class='thumbnail'>"
                    . "<div class='deletefile' onclick='deleteFile(\"$file\")'><i class='fa fa-trash-o'></i></div>"
                    . "<div class='centered'>"
                    . "<video onclick='selecteOneFile(this)'  src='$file' controls></video>"
                    . "</div>"
                    . "</div>"
                    . "</div>";
            }
            
            if ($file_ext == "mp3") {
            $filesview[] = ""
                    . "<div class='col-xs-6 col-sm-4 col-md-3 pull-right content'>"
                    . "<div class='thumbnail'>"
                    . "<div class='centered'>"
                    . "<audio onclick='selecteOneFile(this)'  src='$file' controls></audio>"
                    . "</div>"
                    . "</div>"
                    . "</div>"
            ;
            }
            
            }
        return new JsonModel(array("files" => $filesview, "filesnum" => $filesnum));
    }
}
