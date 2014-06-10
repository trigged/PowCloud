<?php namespace Ext\Pagination;

use Illuminate\Pagination\BootstrapPresenter;

class ExtBootstrapPresenter extends BootstrapPresenter {

    public function getPrevious($text = '&laquo;')
    {
        // If the current page is less than or equal to one, it means we can't go any
        // further back in the pages, so we will render a disabled previous button
        // when that is the case. Otherwise, we will give it an active "status".
        if ($this->currentPage <= 1)
        {
            return '<li class="previous disabled"><span>'.$text.'</span></li>';
        }
        else
        {
            $url = $this->paginator->getUrl($this->currentPage - 1);

            return '<li class="previous"><a href="'.$url.'">'.$text.'</a></li>';
        }
    }

    public function getNext($text = '&raquo;')
    {
        // If the current page is greater than or equal to the last page, it means we
        // can't go any further into the pages, as we're already on this last page
        // that is available, so we will make it the "next" link style disabled.
        if ($this->currentPage >= $this->lastPage)
        {
            return '<li class="next disabled"><span>'.$text.'</span></li>';
        }
        else
        {
            $url = $this->paginator->getUrl($this->currentPage + 1);

            return '<li class="next"><a href="'.$url.'">'.$text.'</a></li>';
        }
    }
}
