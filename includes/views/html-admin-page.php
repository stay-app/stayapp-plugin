<div class="wrap">
    <h1>StayApp</h1>
    <form method="post" action="options.php">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="blogname">Token</label></th>
                    <td>
                        <input name="stay_settings[token]" type="text" id="token" value="<?= get_option('stayapp_token') ?>" class="regular-text">
                        <button type="submit" name="submit" id="validationtoken" class="button button-primary">Validar</button>
                        <img src="<?= site_url() ?>/wp-admin/images/loading.gif" id="load" style="display: none;top: 4px;left: 5px;position: relative;">

                        <img src="<?= plugins_url( 'assets/images/checked.png', dirname(__FILE__) ) ?>" id="checked" alt="" style="<?= (empty(get_option('stayapp_token')) ? 'display:none;' : '') ?>width: 20px;height: 20px;position: relative;top: 5px;left: 5px;">
                        <img src="<?= plugins_url( 'assets/images/close.png', dirname(__FILE__) ) ?>"  id="close" alt="" style="<?= (empty(get_option('stayapp_token')) ? '' : 'display:none;') ?>width: 20px;height: 20px;position: relative;top: 5px;left: 5px;">
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

    <hr style="margin: 40px 0;">

    <h2>Condições de envios</h2>
    <table class="wp-list-table widefat fixed striped pages">
        <thead>
            <tr>
                <th scope="col" class="manage-column">Condição</th>
                <th scope="col" class="manage-column">Valor</th>
                <th scope="col" class="manage-column">Selos</th>
                <th scope="col" class="manage-column">Ação</th>
            </tr>
        </thead>
        <tbody id="the-list">
            <tr>
                <td>Quantidade do Carrinho</td>
                <td>R$ 100,00</td>
                <td>2</td>
                <td>
                    <a class="button button-primary">Editar</a>
                    <a class="button button-primary">Apagar</a>
                </td>
            </tr>
        </tbody>
    </table>

    <hr style="margin: 40px 0;">

    <h2>Criar condições</h2>

    <form>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="blogname">Condição</label>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span>Sua página inicial exibe</span></legend>
                            <p>
                                <label>
                                    <input name="type_condition" type="radio" value="quantity_cart" class="tog" checked="checked">
                                    Quantidade do Carrinho
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input name="type_condition" type="radio" value="product_selected" class="tog">
                                    Produto Selecionado
                                </label>
                            </p>
                            <p>
                                <label>
                                    <input name="type_condition" type="radio" value="always" class="tog">
                                    Sempre
                                </label>
                            </p>
                            <ul>
                                <li>
                                    <label for="page_on_front">Produtos:
                                        <select name="products" id="page_on_front" disabled>
                                            <option value="">— Selecionar —</option>
                                            <?php
                                            $args = array('post_type'      => 'product','posts_per_page' => -1);
                                            $loop = new WP_Query($args);
                                            while ( $loop->have_posts() ) : $loop->the_post();
                                                global $product;
                                                echo '<option class="level-0" value="9"> ' . get_the_title() . ' </option>';
                                            endwhile;
                                            wp_reset_query();
                                            ?>
                                        </select>
                                    </label>
                                </li>
                                <li>
                                    <label for="page_on_front">Valor:
                                        <input type="text" name="value">
                                    </label>
                                </li>
                            </ul>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="blogdescription">Promoção</label></th>
                    <td>
                        <select name="promo" id="promo">
                            <?php
                                foreach ($tickets as $key => $ticket){
                                    if($ticket->is_canceled)
                                        continue;
                            ?>
                                <option value="<?= $key ?>" data-type="<?= $ticket->stamp_type ?>"><?= $ticket->name ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr id="value_condition">
                    <th scope="row"><label for="blogdescription">Quantidade de Selo</label></th>
                    <td>
                        <input name="blogname" type="text" id="blogname" value="" class="regular-text">
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Salvar alterações"></p>
    </form>
</div>