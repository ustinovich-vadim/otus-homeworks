<?php

namespace App\Enums;

enum PostEventTypeEnum: string
{
    case POST_CREATED = 'post_created';
    case POST_UPDATED = 'post_updated';
    case POST_DELETED = 'post_deleted';
}
