<?php
    $settings = get_option('stay_settings');
?>
<div class="wrap">
    <h1>StayApp</h1>
    <form action="options.php" method="post">
        <?php settings_fields('stay_settings') ?>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="blogname">Token</label></th>
                    <td>
                        <input name="stay_settings[token]" type="text" id="blogname" value="<?= $settings['token'] ?>" class="regular-text">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="Validar" style="line-height: 25px;height: 25px;">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="PROMO">Promo</label>
                    </th>
                    <td>
                        <select name="stay_settings[promo]" id="WPLANG">
                            <optgroup label="Financeira">
                                <option value="">Mantenha Clientes</option>
                                <option value="">A cada 10 Combos, ganhe um Combo</option>
                            </optgroup>
                            <optgroup label="Selo">
                                <option>Afrikaans</option>
                                <option>العربية</option>
                            </optgroup>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Habilitar</th>
                    <td>
                        <fieldset>
                            <label for="users_can_register">
                                <input name="stay_settings[enable]" type="checkbox" value="1" <?php checked(1, $settings['enable']) ?>>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button(); ?>
    </form>
</div>