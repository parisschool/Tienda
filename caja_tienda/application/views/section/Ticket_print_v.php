<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Ticket #<?= intval($pago->pago_cliente_id) ?></title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #222;
            margin: 0;
            padding: 20px;
            background: #f0f0f0;
        }
        .ticket {
            max-width: 360px;
            margin: 0 auto;
            background: #fff;
            padding: 20px 18px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        }
        .ticket h1 {
            margin: 0 0 4px 0;
            font-size: 18px;
            text-align: center;
            letter-spacing: 1px;
        }
        .ticket .sub {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-bottom: 14px;
        }
        .meta {
            border-top: 1px dashed #bbb;
            border-bottom: 1px dashed #bbb;
            padding: 10px 0;
            margin-bottom: 12px;
            line-height: 1.5;
        }
        .meta strong { display: inline-block; min-width: 90px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        th, td {
            padding: 6px 2px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }
        th { color: #555; font-weight: 700; }
        td.num, th.num { text-align: right; }
        td.center, th.center { text-align: center; }
        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            padding-top: 8px;
            border-top: 2px solid #222;
        }
        .estado {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .estado.pagado { background: #d4edda; color: #155724; }
        .estado.anulado { background: #f8d7da; color: #721c24; }
        .estado.pendiente { background: #fff3cd; color: #856404; }
        .acciones {
            text-align: center;
            margin-top: 18px;
        }
        .acciones button {
            border: none;
            background: #2b3e50;
            color: #fff;
            padding: 10px 18px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            margin: 0 4px;
        }
        .acciones button.secondary {
            background: #888;
        }
        .vacio {
            text-align: center;
            color: #888;
            padding: 12px 0;
            font-style: italic;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .ticket { box-shadow: none; max-width: 100%; border-radius: 0; }
            .acciones { display: none; }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <h1>TICKET DE VENTA</h1>
        <div class="sub">Transacción #<?= intval($pago->pago_cliente_id) ?></div>

        <div class="meta">
            <div><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pago->pago_cliente_fecha)) ?></div>
            <div><strong>Cliente:</strong> <?= htmlspecialchars(trim($pago->cliente) !== '' ? $pago->cliente : 'CONSUMIDOR FINAL') ?></div>
            <div><strong>Cobrador:</strong> <?= htmlspecialchars($pago->cobrador) ?></div>
            <div><strong>Pago:</strong> <?= htmlspecialchars($pago->pago_forma_descripcion ? $pago->pago_forma_descripcion : 'Efectivo') ?></div>
            <div>
                <strong>Estado:</strong>
                <span class="estado <?= strtolower($pago->pago_cliente_estado) ?>"><?= htmlspecialchars($pago->pago_cliente_estado) ?></span>
            </div>
        </div>

        <?php if (!empty($detalle)): ?>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="center">Cant</th>
                    <th class="num">P.Unit</th>
                    <th class="num">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalle as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item->producto_nombre) ?></td>
                    <td class="center"><?= intval($item->cantidad) ?></td>
                    <td class="num">$<?= number_format(floatval($item->precio_unitario), 2) ?></td>
                    <td class="num">$<?= number_format(floatval($item->subtotal), 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="vacio">Sin desglose de productos para este ticket.</div>
        <?php endif; ?>

        <div class="total">
            TOTAL: $<?= number_format(floatval($pago->pago_cliente_monto_total), 2) ?>
        </div>

        <div class="acciones">
            <button type="button" onclick="window.print()">Imprimir</button>
            <button type="button" class="secondary" onclick="window.close()">Cerrar</button>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() { window.print(); }, 300);
        });
    </script>
</body>
</html>
