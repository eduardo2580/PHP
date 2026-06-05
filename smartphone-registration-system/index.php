<?php
// CLASSE SMARTPHONE - feita por mim mesmo hehe

class Smartphone {
    private $modelo;
    private $marca;
    private $armazenamento; // em GB!!!
    private $memoriaRam;    // tambem GB
    private $sistemaOperacional;
    private $temaPreferido; // light ou dark
    private $emailFornecedor;
    private $precoCusto;
    private $precoVenda;

    // getters e setters
    public function getModelo() { return $this->modelo; }
    public function setModelo($modelo) { $this->modelo = $modelo; }

    public function getMarca() { return $this->marca; }
    public function setMarca($marca) { $this->marca = $marca; }

    public function getArmazenamento() { return $this->armazenamento; }
    public function setArmazenamento($armazenamento) { $this->armazenamento = $armazenamento; }

    public function getMemoriaRam() { return $this->memoriaRam; }
    public function setMemoriaRam($memoriaRam) { $this->memoriaRam = $memoriaRam; }

    public function getSistemaOperacional() { return $this->sistemaOperacional; }
    public function setSistemaOperacional($sistemaOperacional) { $this->sistemaOperacional = $sistemaOperacional; }

    public function getTemaPreferido() { return $this->temaPreferido; }
    public function setTemaPreferido($temaPreferido) { $this->temaPreferido = $temaPreferido; }

    public function getEmailFornecedor() { return $this->emailFornecedor; }
    public function setEmailFornecedor($emailFornecedor) { $this->emailFornecedor = $emailFornecedor; }

    public function getPrecoCusto() { return $this->precoCusto; }
    public function setPrecoCusto($precoCusto) { $this->precoCusto = $precoCusto; }

    public function getPrecoVenda() { return $this->precoVenda; }
    public function setPrecoVenda($precoVenda) { $this->precoVenda = $precoVenda; }

    // esse metodo calcula o lucro!! simples assim rsrs
    public function calculaMargemBruta() {
        return $this->precoVenda - $this->precoCusto;
    }
}

// COOKIE DO TEMA
$temaAtual = "light"; // padrao e light

if (isset($_POST['trocarTema'])) {
    // trocar o tema quando clicar no botao
    $novoTema = (($_COOKIE['tema'] ?? 'light') === 'dark') ? 'light' : 'dark';
    setcookie('tema', $novoTema, time() + (86400 * 30), "/");
    $temaAtual = $novoTema;
} elseif (isset($_COOKIE['tema'])) {
    $temaAtual = $_COOKIE['tema'] === 'dark' ? 'dark' : 'light';
}

// PROCESSAR FORMULARIO
$smartphone = null;
$erro = '';
$fatoCurioso = '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['trocarTema'])) {

    // verificar se todos campos foram preenchidos
    $campos = ['modelo','marca','armazenamento','memoriaRam',
               'sistemaOperacional','temaPreferido','emailFornecedor',
               'precoCusto','precoVenda'];

    $ok = true;
    foreach ($campos as $c) {
        if (empty($_POST[$c])) {
            $ok = false;
            break; // para o loop
        }
    }

    if ($ok) {
        // criar objeto e setar tudo
        $smartphone = new Smartphone();
        $smartphone->setModelo($_POST['modelo']);
        $smartphone->setMarca($_POST['marca']);
        $smartphone->setArmazenamento((int)$_POST['armazenamento']);
        $smartphone->setMemoriaRam((int)$_POST['memoriaRam']);
        $smartphone->setSistemaOperacional($_POST['sistemaOperacional']);
        $smartphone->setTemaPreferido($_POST['temaPreferido']);
        $smartphone->setEmailFornecedor($_POST['emailFornecedor']);
        $smartphone->setPrecoCusto((float)$_POST['precoCusto']);
        $smartphone->setPrecoVenda((float)$_POST['precoVenda']);

        // salvar o tema no cookie tbm
        setcookie('tema', $smartphone->getTemaPreferido(), time() + (86400 * 30), "/");
        $temaAtual = $smartphone->getTemaPreferido();

        // API dos numeros
        $gb = $smartphone->getArmazenamento();
        $apiUrl = "http://numbersapi.com/{$gb}";
        $ctx = stream_context_create(['http' => ['timeout' => 5]]);
        $fato = @file_get_contents($apiUrl, false, $ctx);
        $fatoCurioso = $fato !== false
            ? htmlspecialchars($fato)
            : "Nao foi possivel buscar um fato sobre {$gb} agora. Sem internet? :/";

        $sucesso = true;

    } else {
        $erro = "Ops!! Preenche todos os campos ai por favor";
    }
}

$isDark = $temaAtual === 'dark';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Smartphone</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* meus estilos personalizados!! */

        body {
            font-family: 'Nunito', sans-serif;
            min-height: 100vh;
            transition: background-color 0.4s ease, color 0.4s ease;
        }

        /* tema claro */
        body.tema-light {
            background: linear-gradient(135deg, #e0f2fe 0%, #f0fdf4 50%, #fef9c3 100%);
            color: #1e293b;
        }

        /* tema escuro */
        body.tema-dark {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            color: #e2e8f0;
        }

        .meu-header {
            padding: 2rem 0 1rem;
            text-align: center;
        }

        .meu-header h1 {
            font-weight: 800;
            font-size: 2.2rem;
            letter-spacing: -0.5px;
        }

        .tema-light .meu-header h1 { color: #0369a1; }
        .tema-dark  .meu-header h1 { color: #7dd3fc; }

        .meu-header p.subtitulo {
            font-size: 0.95rem;
            opacity: 0.65;
        }

        /* botao de trocar tema */
        .btn-tema {
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0.4rem 1.1rem;
            transition: all 0.2s;
            border: 2px solid currentColor;
            background: none;
            cursor: pointer;
        }

        .tema-light .btn-tema { color: #0369a1; background: white; }
        .tema-light .btn-tema:hover { background: #0369a1; color: white; }
        .tema-dark  .btn-tema { color: #7dd3fc; background: transparent; }
        .tema-dark  .btn-tema:hover { background: #7dd3fc; color: #0f172a; }

        /* card */
        .meu-card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
            transition: background 0.4s;
        }

        .tema-light .meu-card {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(10px);
        }
        .tema-dark .meu-card {
            background: rgba(30, 27, 75, 0.7);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        }

        /* inputs escuros */
        .tema-dark .form-control,
        .tema-dark .form-select {
            background-color: #1e293b;
            border-color: #334155;
            color: #e2e8f0;
        }
        .tema-dark .form-control:focus,
        .tema-dark .form-select:focus {
            background-color: #1e293b;
            border-color: #7dd3fc;
            color: #e2e8f0;
            box-shadow: 0 0 0 3px rgba(125,211,252,0.2);
        }
        .tema-dark .form-control::placeholder { color: #64748b; }
        .tema-light .form-control:focus,
        .tema-light .form-select:focus {
            border-color: #0369a1;
            box-shadow: 0 0 0 3px rgba(3,105,161,0.15);
        }

        .form-label {
            font-weight: 700;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.35rem;
        }
        .tema-light .form-label { color: #475569; }
        .tema-dark  .form-label { color: #94a3b8; }

        .btn-cadastrar {
            border-radius: 12px;
            font-weight: 800;
            font-size: 1rem;
            padding: 0.75rem;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .btn-cadastrar:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(3,105,161,0.35);
        }

        .detalhe-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.7rem 1.1rem;
            border-radius: 10px;
            margin-bottom: 0.4rem;
            font-size: 0.95rem;
            transition: background 0.2s;
        }
        .tema-light .detalhe-item { background: #f1f5f9; }
        .tema-light .detalhe-item:hover { background: #e2e8f0; }
        .tema-dark  .detalhe-item { background: rgba(255,255,255,0.06); }
        .tema-dark  .detalhe-item:hover { background: rgba(255,255,255,0.10); }

        .label-key {
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .tema-light .label-key { color: #64748b; }
        .tema-dark  .label-key { color: #94a3b8; }

        .valor { font-weight: 700; font-size: 0.95rem; }
        .tema-light .valor { color: #0f172a; }
        .tema-dark  .valor { color: #f1f5f9; }

        .margem-box {
            border-radius: 14px;
            padding: 1.1rem 1.3rem;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        .tema-light .margem-box { background: linear-gradient(90deg,#dcfce7,#d1fae5); border: 1.5px solid #86efac; }
        .tema-dark  .margem-box { background: rgba(134,239,172,0.12); border: 1.5px solid #4ade80; }

        .margem-valor { font-size: 1.5rem; font-weight: 800; }
        .tema-light .margem-valor { color: #15803d; }
        .tema-dark  .margem-valor { color: #4ade80; }

        .fato-box {
            border-radius: 14px;
            padding: 1rem 1.2rem;
            margin-top: 0.8rem;
            font-size: 0.92rem;
            line-height: 1.6;
        }
        .tema-light .fato-box { background: linear-gradient(90deg,#eff6ff,#e0f2fe); border: 1.5px solid #93c5fd; }
        .tema-dark  .fato-box { background: rgba(147,197,253,0.10); border: 1.5px solid #3b82f6; }

        .opcao-tema {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 1rem;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.88rem;
            border: 2px solid transparent;
            transition: all 0.18s;
            user-select: none;
        }
        .tema-light .opcao-tema { background: #f1f5f9; color: #475569; border-color: #cbd5e1; }
        .tema-light .opcao-tema:has(input:checked) { background: #0369a1; color: white; border-color: #0369a1; }
        .tema-dark  .opcao-tema { background: rgba(255,255,255,0.07); color: #94a3b8; border-color: #334155; }
        .tema-dark  .opcao-tema:has(input:checked) { background: #7dd3fc; color: #0f172a; border-color: #7dd3fc; }
        .opcao-tema input { display: none; }

        .section-title {
            font-weight: 800;
            font-size: 1.05rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .tema-light .section-title { color: #0369a1; }
        .tema-dark  .section-title { color: #7dd3fc; }

        hr.meu-hr { opacity: 0.12; margin: 1.2rem 0; }

        .card-resultado { animation: slideUp 0.4s ease; }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .badge-so { border-radius: 8px; font-size: 0.8rem; font-weight: 700; padding: 0.3rem 0.7rem; }

        .rodape { text-align: center; font-size: 0.8rem; opacity: 0.4; padding: 2rem 0 1rem; }

        .alert-meu-erro { border-radius: 12px; font-weight: 700; border: none; }
        .tema-light .alert-meu-erro { background: #fef2f2; color: #dc2626; }
        .tema-dark  .alert-meu-erro { background: rgba(220,38,38,0.15); color: #fca5a5; }

        .tema-dark select option { background-color: #1e293b; color: #e2e8f0; }

        .topo-header { display: flex; justify-content: flex-end; padding-top: 1.2rem; }
    </style>
</head>
<body class="tema-<?php echo $temaAtual; ?>">

<div class="container" style="max-width:640px;">

    <!-- botao de trocar tema no topo -->
    <div class="topo-header">
        <form method="POST" action="">
            <input type="hidden" name="trocarTema" value="1">
            <button type="submit" class="btn-tema">
                <?php if ($isDark): ?>
                    <i class="bi bi-sun-fill me-1"></i> Modo Claro
                <?php else: ?>
                    <i class="bi bi-moon-stars-fill me-1"></i> Modo Escuro
                <?php endif; ?>
            </button>
        </form>
    </div>

    <!-- HEADER -->
    <div class="meu-header">
        <div style="font-size:2.5rem; margin-bottom:0.3rem;">&#128242;</div>
        <h1>Cadastro de Smartphone</h1>
        <p class="subtitulo">Preencha os dados tecnico-comerciais do aparelho</p>
    </div>

    <!-- MENSAGEM DE ERRO -->
    <?php if ($erro): ?>
    <div class="alert alert-meu-erro mb-3 px-3 py-2">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $erro; ?>
    </div>
    <?php endif; ?>

    <!-- CARD DO FORMULARIO -->
    <div class="meu-card p-4 mb-4">
        <form method="POST" action="">

            <div class="section-title">
                <i class="bi bi-cpu"></i> Dados Tecnicos
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label class="form-label" for="marca">Marca</label>
                    <input type="text" class="form-control" id="marca" name="marca"
                           placeholder="Ex: Samsung, Apple, Xiaomi..."
                           value="<?php echo htmlspecialchars($_POST['marca'] ?? ''); ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label" for="modelo">Modelo</label>
                    <input type="text" class="form-control" id="modelo" name="modelo"
                           placeholder="Ex: Galaxy S24, iPhone 15 Pro..."
                           value="<?php echo htmlspecialchars($_POST['modelo'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label" for="armazenamento">
                        <i class="bi bi-device-hdd me-1"></i>Armazenamento (GB)
                    </label>
                    <input type="number" class="form-control" id="armazenamento" name="armazenamento"
                           placeholder="256" min="1"
                           value="<?php echo htmlspecialchars($_POST['armazenamento'] ?? ''); ?>" required>
                </div>
                <div class="col-6">
                    <label class="form-label" for="memoriaRam">
                        <i class="bi bi-memory me-1"></i>RAM (GB)
                    </label>
                    <input type="number" class="form-control" id="memoriaRam" name="memoriaRam"
                           placeholder="8" min="1"
                           value="<?php echo htmlspecialchars($_POST['memoriaRam'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="sistemaOperacional">Sistema Operacional</label>
                <select class="form-select" id="sistemaOperacional" name="sistemaOperacional" required>
                    <option value="">Selecione o sistema...</option>
                    <?php
                    $sos = ['Android','iOS','HarmonyOS','Outro'];
                    foreach ($sos as $so):
                        $sel = (($_POST['sistemaOperacional'] ?? '') === $so) ? 'selected' : '';
                    ?>
                    <option value="<?php echo $so; ?>" <?php echo $sel; ?>><?php echo $so; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <hr class="meu-hr">

            <div class="section-title">
                <i class="bi bi-currency-dollar"></i> Dados Comerciais
            </div>

            <div class="mb-3">
                <label class="form-label" for="emailFornecedor">
                    <i class="bi bi-envelope me-1"></i>E-mail do Fornecedor
                </label>
                <input type="email" class="form-control" id="emailFornecedor" name="emailFornecedor"
                       placeholder="fornecedor@empresa.com"
                       value="<?php echo htmlspecialchars($_POST['emailFornecedor'] ?? ''); ?>" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label" for="precoCusto">Preco de Custo (R$)</label>
                    <input type="number" step="0.01" class="form-control" id="precoCusto" name="precoCusto"
                           placeholder="1200.00" min="0"
                           value="<?php echo htmlspecialchars($_POST['precoCusto'] ?? ''); ?>" required>
                </div>
                <div class="col-6">
                    <label class="form-label" for="precoVenda">Preco de Venda (R$)</label>
                    <input type="number" step="0.01" class="form-control" id="precoVenda" name="precoVenda"
                           placeholder="1899.00" min="0"
                           value="<?php echo htmlspecialchars($_POST['precoVenda'] ?? ''); ?>" required>
                </div>
            </div>

            <hr class="meu-hr">

            <div class="section-title">
                <i class="bi bi-palette"></i> Tema da Interface
            </div>

            <div class="d-flex gap-2 mb-4">
                <label class="opcao-tema">
                    <input type="radio" name="temaPreferido" value="light"
                           <?php echo (($_POST['temaPreferido'] ?? $temaAtual) === 'light') ? 'checked' : ''; ?>>
                    <i class="bi bi-sun-fill"></i> Claro
                </label>
                <label class="opcao-tema">
                    <input type="radio" name="temaPreferido" value="dark"
                           <?php echo (($_POST['temaPreferido'] ?? $temaAtual) === 'dark') ? 'checked' : ''; ?>>
                    <i class="bi bi-moon-stars-fill"></i> Escuro
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-cadastrar w-100">
                <i class="bi bi-floppy me-2"></i>Cadastrar Smartphone
            </button>

        </form>
    </div>

    <!-- RESULTADO DO CADASTRO -->
    <?php if ($sucesso && $smartphone instanceof Smartphone): ?>
    <div class="meu-card p-4 mb-4 card-resultado">

        <div class="section-title mb-3">
            <i class="bi bi-check-circle-fill text-success"></i>
            Smartphone Cadastrado com Sucesso!
        </div>

        <div class="detalhe-item">
            <span class="label-key"><i class="bi bi-tag me-1"></i>Marca</span>
            <span class="valor"><?php echo htmlspecialchars($smartphone->getMarca()); ?></span>
        </div>
        <div class="detalhe-item">
            <span class="label-key"><i class="bi bi-phone me-1"></i>Modelo</span>
            <span class="valor"><?php echo htmlspecialchars($smartphone->getModelo()); ?></span>
        </div>
        <div class="detalhe-item">
            <span class="label-key"><i class="bi bi-device-hdd me-1"></i>Armazenamento</span>
            <span class="valor"><?php echo $smartphone->getArmazenamento(); ?> GB</span>
        </div>
        <div class="detalhe-item">
            <span class="label-key"><i class="bi bi-memory me-1"></i>RAM</span>
            <span class="valor"><?php echo $smartphone->getMemoriaRam(); ?> GB</span>
        </div>
        <div class="detalhe-item">
            <span class="label-key"><i class="bi bi-gear me-1"></i>Sistema</span>
            <span class="valor">
                <?php
                $soColors = ['Android'=>'success','iOS'=>'dark','HarmonyOS'=>'danger','Outro'=>'secondary'];
                $color = $soColors[$smartphone->getSistemaOperacional()] ?? 'secondary';
                ?>
                <span class="badge bg-<?php echo $color; ?> badge-so">
                    <?php echo htmlspecialchars($smartphone->getSistemaOperacional()); ?>
                </span>
            </span>
        </div>
        <div class="detalhe-item">
            <span class="label-key"><i class="bi bi-envelope me-1"></i>Fornecedor</span>
            <span class="valor" style="font-size:0.85rem;"><?php echo htmlspecialchars($smartphone->getEmailFornecedor()); ?></span>
        </div>
        <div class="detalhe-item">
            <span class="label-key"><i class="bi bi-arrow-down-circle me-1"></i>Custo</span>
            <span class="valor">R$ <?php echo number_format($smartphone->getPrecoCusto(), 2, ',', '.'); ?></span>
        </div>
        <div class="detalhe-item">
            <span class="label-key"><i class="bi bi-arrow-up-circle me-1"></i>Venda</span>
            <span class="valor">R$ <?php echo number_format($smartphone->getPrecoVenda(), 2, ',', '.'); ?></span>
        </div>

        <div class="margem-box">
            <div style="font-size:1.8rem;">&#128176;</div>
            <div>
                <div style="font-size:0.78rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; opacity:0.65;">Margem Bruta (lucro/unidade)</div>
                <div class="margem-valor">
                    R$ <?php echo number_format($smartphone->calculaMargemBruta(), 2, ',', '.'); ?>
                </div>
            </div>
        </div>

        <?php if (!empty($fatoCurioso)): ?>
        <div class="fato-box">
            <div style="font-size:0.75rem; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; opacity:0.6; margin-bottom:0.3rem;">
                <i class="bi bi-lightbulb me-1"></i>Fato curioso sobre <?php echo $smartphone->getArmazenamento(); ?>
            </div>
            <div><?php echo $fatoCurioso; ?></div>
        </div>
        <?php endif; ?>

    </div>
    <?php endif; ?>

    <div class="rodape">
        feito com amor por mim &bull; trabalho de PHP &bull; <?php echo date('Y'); ?>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // atualizar tema visualmente quando muda o radio (o cookie salva no submit)
    document.querySelectorAll('input[name="temaPreferido"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            var body = document.body;
            if (this.value === 'dark') {
                body.classList.remove('tema-light');
                body.classList.add('tema-dark');
            } else {
                body.classList.remove('tema-dark');
                body.classList.add('tema-light');
            }
        });
    });
</script>

</body>
</html>