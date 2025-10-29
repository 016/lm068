<?php

namespace App\Controllers\Backend;

use App\Constants\AdminUserRole;
use App\Core\Request;
use App\Models\Collection;
use App\Models\Tag;
use App\Models\Content;
use App\Constants\TagStatus;
use App\Constants\ContentStatus;

/**
 * test controller for backend dev
 */
class TController extends BackendController
{
    public function index(): void
    {
        //// manual update tag and collection linked content count in db.
        //0 load all tags
        $tags = Tag::findAll();
        foreach ($tags as $tag) {
            //1 loop and update all tags cnt
            $tmpTag = new Tag();
            $tmpTag->updateContentCount($tag['id']);
        }
        //0 load all collections
        $collections = Collection::findAll();
        foreach ($collections as $collection) {
            //1 loop and update all collections cnt
            $tmpCollection = new Collection();
            $tmpCollection->updateContentCount($collection['id']);
        }

    }

}