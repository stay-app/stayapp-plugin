<?php
class someClass
{
    const LANG = 'some_textdomain';

    public function __construct()
    {
        add_action( 'add_meta_boxes', array( &$this, 'add_some_meta_box' ) );
    }

    /**
     * Adiciona a meta box
     */
    public function add_some_meta_box()
    {
        add_meta_box(
            'some_meta_box_name'
            ,__( 'Some Meta Box Headline', self::LANG )
            ,array( &$this, 'render_meta_box_content' )
            ,'post'
            ,'advanced'
            ,'high'
        );
    }


    /**
     * O conteúdo da meta box
     */
    public function render_meta_box_content()
    {
        echo '<h1>TESTE - esta mensagem estará dentro da meta box</h1>';
    }
}