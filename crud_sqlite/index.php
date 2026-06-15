<?php
declare(strict_types=1);

require_once __DIR__ . '/Musica.php';
require_once __DIR__ . '/MusicaDAO.php';
require_once __DIR__ . '/DatabaseConnection.php';

// ── Bootstrap ────────────────────────────────────────────────────────────────
$db = new DatabaseConnection();
$db->connect();
$dao = new MusicaDAO($db->getConnection());

$erro    = '';
$sucesso = '';
$editando = null; // Musica|null

// ── Actions ──────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $nome  = trim($_POST['nome']  ?? '');
    $autor = trim($_POST['autor'] ?? '');
    $id    = isset($_POST['id']) ? (int) $_POST['id'] : null;

    if ($nome === '' || $autor === '') {
        $erro = 'Nome e Autor são obrigatórios.';
    } else {
        switch ($_POST['acao']) {
            case 'inserir':
                $dao->criarMusica(new Musica(null, $nome, $autor));
                $sucesso = 'Música inserida com sucesso!';
                break;

            case 'atualizar':
                if ($id && $dao->atualizarMusica(new Musica($id, $nome, $autor))) {
                    $sucesso = 'Música atualizada com sucesso!';
                } else {
                    $erro = 'Música não encontrada para atualização.';
                }
                break;
        }
    }
} elseif (isset($_GET['excluir'])) {
    $id = (int) $_GET['excluir'];
    if ($dao->excluirMusica($id)) {
        $sucesso = 'Música excluída com sucesso!';
    } else {
        $erro = 'Música não encontrada.';
    }
} elseif (isset($_GET['editar'])) {
    $editando = $dao->buscarMusica((int) $_GET['editar']);
    if (!$editando) {
        $erro = 'Música não encontrada.';
    }
}

$musicas = $dao->buscarTodas();

// ── Helper ───────────────────────────────────────────────────────────────────
function h(mixed $v): string {
    return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRUD Músicas</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #f0f4f8;
            color: #1a202c;
            padding: 2rem 1rem;
        }

        h1 { font-size: 1.75rem; margin-bottom: 1.5rem; color: #2d3748; }
        h2 { font-size: 1.15rem; margin-bottom: 1rem; color: #4a5568; }

        .container { max-width: 760px; margin: 0 auto; }

        /* ── Alerts ── */
        .alert {
            padding: .75rem 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: .9rem;
        }
        .alert-success { background: #c6f6d5; color: #22543d; border: 1px solid #9ae6b4; }
        .alert-error   { background: #fed7d7; color: #742a2a; border: 1px solid #fc8181; }

        /* ── Table ── */
        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            overflow-x: auto;
        }

        table { width: 100%; border-collapse: collapse; font-size: .9rem; }
        th, td { padding: .6rem .9rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #edf2f7; font-weight: 600; color: #4a5568; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f7fafc; }

        .actions a {
            display: inline-block;
            padding: .25rem .65rem;
            border-radius: 4px;
            font-size: .8rem;
            text-decoration: none;
            margin-right: .25rem;
        }
        .btn-edit   { background: #ebf8ff; color: #2b6cb0; border: 1px solid #bee3f8; }
        .btn-delete { background: #fff5f5; color: #c53030; border: 1px solid #fed7d7; }
        .btn-edit:hover   { background: #bee3f8; }
        .btn-delete:hover { background: #fed7d7; }

        .empty { text-align: center; color: #a0aec0; padding: 1.5rem 0; font-style: italic; }

        /* ── Form ── */
        .form-group { margin-bottom: .85rem; }
        label { display: block; font-size: .85rem; font-weight: 600; margin-bottom: .3rem; color: #4a5568; }

        input[type="text"] {
            width: 100%;
            padding: .5rem .75rem;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            font-size: .95rem;
            outline: none;
            transition: border-color .15s;
        }
        input[type="text"]:focus { border-color: #4299e1; box-shadow: 0 0 0 3px rgba(66,153,225,.15); }

        .btn-row { display: flex; gap: .5rem; margin-top: 1rem; }

        button {
            padding: .5rem 1.2rem;
            border: none;
            border-radius: 6px;
            font-size: .9rem;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-primary { background: #4299e1; color: #fff; }
        .btn-primary:hover { background: #3182ce; }
        .btn-secondary { background: #e2e8f0; color: #4a5568; }
        .btn-secondary:hover { background: #cbd5e0; }
        .btn-warning { background: #ed8936; color: #fff; }
        .btn-warning:hover { background: #dd6b20; }
    </style>
</head>
<body>
<div class="container">
    <h1>🎵 CRUD Músicas</h1>

    <?php if ($sucesso): ?>
        <div class="alert alert-success"><?= h($sucesso) ?></div>
    <?php endif; ?>
    <?php if ($erro): ?>
        <div class="alert alert-error"><?= h($erro) ?></div>
    <?php endif; ?>

    <!-- ── Tabela ── -->
    <div class="card">
        <h2>Músicas cadastradas</h2>
        <?php if ($musicas): ?>
        <table>
            <thead>
                <tr><th>#</th><th>Nome</th><th>Autor</th><th>Ações</th></tr>
            </thead>
            <tbody>
            <?php foreach ($musicas as $m): ?>
                <tr>
                    <td><?= h($m->getId()) ?></td>
                    <td><?= h($m->getNome()) ?></td>
                    <td><?= h($m->getAutor()) ?></td>
                    <td class="actions">
                        <a class="btn-edit"
                           href="?editar=<?= h($m->getId()) ?>">✏️ Editar</a>
                        <a class="btn-delete"
                           href="?excluir=<?= h($m->getId()) ?>"
                           onclick="return confirm('Excluir <?= h(addslashes($m->getNome())) ?>?')">🗑️ Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="empty">Nenhuma música cadastrada ainda.</p>
        <?php endif; ?>
    </div>

    <!-- ── Formulário ── -->
    <div class="card">
        <h2><?= $editando ? '✏️ Editar música' : '➕ Inserir música' ?></h2>
        <form method="POST" action="">
            <input type="hidden" name="acao"
                   value="<?= $editando ? 'atualizar' : 'inserir' ?>">
            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?= h($editando->getId()) ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" required
                       placeholder="Nome da música"
                       value="<?= h($editando?->getNome() ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="autor">Autor</label>
                <input type="text" id="autor" name="autor" required
                       placeholder="Nome do autor / banda"
                       value="<?= h($editando?->getAutor() ?? '') ?>">
            </div>

            <div class="btn-row">
                <?php if ($editando): ?>
                    <button type="submit" class="btn-warning">💾 Atualizar</button>
                    <a href="index.php"><button type="button" class="btn-secondary">✖ Cancelar</button></a>
                <?php else: ?>
                    <button type="submit" class="btn-primary">➕ Inserir</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
</body>
</html>
