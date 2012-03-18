ALTER TABLE ##_dg_gallery_comments
        ADD `ns_level` INT(11) DEFAULT NULL;
ALTER TABLE ##_dg_gallery_comments
        ADD `ns_right` BIGINT(20) NOT NULL DEFAULT 0;
ALTER TABLE ##_dg_gallery_comments
        ADD `ns_left` BIGINT(20) NOT NULL DEFAULT 0 