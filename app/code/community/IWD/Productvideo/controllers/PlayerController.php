<?php
class IWD_Productvideo_PlayerController extends Mage_Core_Controller_Front_Action
{
    public function getvideoAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $result['status'] = 1;
            try {
                $result['id'] = $data["video_id"];
                $video = Mage::getModel("iwd_productvideo/video")->load($data["video_id"]);
                $result['description'] = $video->getDescription();
                $result['title'] = $video->getTitle();
                $result['type'] = (Mage::helper('iwd_productvideo')->isLocalVideoPlayer($video)) ? 'local' : $video->getVideoType();

                $this->loadLayout();
                $result['embed_code'] = $this->getLayout()
                    ->createBlock('iwd_productvideo/frontend_player')
                    ->setData('video', $video)
                    ->setTemplate('iwd/productvideo/player.phtml')
                    ->toHtml();
            } catch (Exception $e) {
                $result['status'] = 0;
                $result['error'] = $e->getMessage();
            }

            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }
    /*iwd fix*/
    protected function _isAllowed()
    {
        return true;
    }
}