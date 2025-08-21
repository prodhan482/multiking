package com.hbworks.eu.bkashbd.data.model

import com.hbworks.eu.bkashbd.data.local_db.entity.Article

class ArticleRespons {

    var total_rows = 0
    lateinit var articles: List<Article>

}