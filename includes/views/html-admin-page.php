<div class="wrap">
    <h1>StayApp</h1>
    <form method="post" action="options.php" style="border-bottom: 2px solid #afafaf61;padding-bottom: 20px;margin-bottom: 40px;">
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
</div>