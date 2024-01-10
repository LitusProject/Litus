<?php

namespace PageBundle\Controller;

use Laminas\Http\Headers;
use Laminas\View\Model\ViewModel;
use PageBundle\Entity\CategoryPage;
use PageBundle\Entity\Frame;
use PageBundle\Entity\Link;
use PageBundle\Entity\Node\Page;

/**
 * PageController
 *
 * @author Robbe Serry <robbe.serry@vtk.be>
 */
class CategoryPageController extends \CommonBundle\Component\Controller\ActionController\SiteController
{
    public function viewAction()
    {
        $page = $this->getCategoryPageEntity();
        if ($page === null) {
            return $this->notFoundAction();
        }

        $big_frames = $this->getEntityManager()
            ->getRepository("PageBundle\Entity\Frame")
            ->findAllActiveBigFrames($page)
            ->getResult();

        $small_frames = $this->getEntityManager()
            ->getRepository("PageBundle\Entity\Frame")
            ->findAllActiveSmallFrames($page)
            ->getResult();

        $result_big = array();
        foreach ($big_frames as $frame) {
            $frame_data = array();
            $frame_data['frame'] = $frame;
            $frame_data['frame_type'] = 'Big Frame';

            if ($frame->getLinkTo() instanceof Page) {
                $frame_data['linkto_type'] = 'page';
            } else if ($frame->getLinkTo() instanceof Link) {
                $frame_data['linkto_type'] = 'link';
            }

            $result_big[] = $frame_data;
        }

        $result_small = array();
        foreach ($small_frames as $frame) {
            $frame_data = array();
            $frame_data['frame'] = $frame;

            if ($frame->hasDescription()) {
                $frame_data['frame_type'] = 'Small Frame with Description';
            } else if ($frame->hasPoster()) {
                $frame_data['frame_type'] = 'Small Frame with Poster';
            }

            if ($frame->getLinkTo() instanceof Page) {
                $frame_data['linkto_type'] = 'page';
            } else if ($frame->getLinkTo() instanceof Link) {
                $frame_data['linkto_type'] = 'link';
            }

            $result_small[] = $frame_data;
        }

        return new ViewModel(
            array(
                'category_page' => $page,
                'big_frames'    => $result_big,
                'small_frames'  => $result_small,
                'fathom'        => $this->getFathomInfo(),
            )
        );
    }

    public function posterAction()
    {
        $frame = $this->getFrameEntityByPoster();
        if ($frame === null) {
            return $this->notFoundAction();
        }

        $filePath = $this->getEntityManager()
                ->getRepository('CommonBundle\Entity\General\Config')
                ->getConfigValue('page.frame_poster_path') . '/';

        $headers = new Headers();
        $headers->addHeaders(
            array(
                'Content-Type' => mime_content_type($filePath . $frame->getPoster()),
            )
        );
        $this->getResponse()->setHeaders($headers);

        $handle = fopen($filePath . $frame->getPoster(), 'r');
        $data = fread($handle, filesize($filePath . $frame->getPoster()));
        fclose($handle);

        return new ViewModel(
            array(
                'data' => $data,
            )
        );
    }

    /**
     * @return CategoryPage|null
     */
    private function getCategoryPageEntity()
    {
        $categories = $this->getEntityManager()
            ->getRepository('PageBundle\Entity\Category')
            ->findByParent(null);

        $languages = $this->getEntityManager()
            ->getRepository('CommonBundle\Entity\General\Language')
            ->findAll();

        $name = $this->getParam('name');
        $page = null;
        foreach ($categories as $category) {
            foreach ($languages as $language) {
                if ($name == $category->getName($language)) {
                    $page = $this->getEntityManager()
                        ->getRepository('PageBundle\Entity\CategoryPage')
                        ->findOneByCategory($category);
                    break;
                }
            }
        }

        if (!($page instanceof CategoryPage)) {
            return;
        }

        return $page;
    }

    /**
     * @return Frame|null
     */
    private function getFrameEntityByPoster()
    {
        $frame = $this->getEntityById('PageBundle\Entity\Frame', 'poster_name', 'poster');

        if (!($frame instanceof Frame)) {
            return;
        }

        return $frame;
    }
}
