<?php

// ESTA EM segmentos_diagnostic
// DELETAR ARQUIVOS OBSOLETOS ACIMA DE 10 DIAS
$PASTA_INICIAL = dirname(__FILE__);
$PASTA_INICIAL = str_replace("\\", "/", $PASTA_INICIAL);

$PASTA_NFEFILESx = $PASTA_INICIAL . '/nfefiles';

// verificar permiss�o da pasta nfefiles
$filenfefiles = $PASTA_NFEFILESx;

$perms = fileperms($filenfefiles);
$xperm = substr(decoct($perms), 2);

if ($xperm <> '777' and $xperm <> '755') {
    echo "Prezado Administrador, favor colocar permiss�o  755  para pasta $PASTA_NFEFILESx";
    exit;
}

$TPOS_VENDAS = "pos_vendas";
$CONTTPOS_VENDAS = $conn02;
$tabela_sis[138] = $TPOS_VENDAS;

$TAPARTAMENTOS_RESERVAS = "apartamentos_reservas";
$CONTAPATAMENTOS_RESERVAS = $conn02;
$tabela_sis[137] = $TAPARTAMENTOS_RESERVAS;

$TITEM_FLUXO_LOG = "item_fluxo_log";
$CONTITEM_FLUXO_LOG = $conn02;
$tabela_sis[136] = $TITEM_FLUXO_LOG;

$TITEM_FLUXO_MIRROR = "item_fluxo_mirror";
$CONTITEM_FLUXO_MIRROR = $conn02;
$tabela_sis[135] = $TITEM_FLUXO_MIRROR;

$TJOBS_MED_LAUDOS_MIRROR = 'jobs_med_laudos_mirror';
$CONTJOBS_MED_LAUDOS_MIRROR = $conn02;
$tabela_sis[134] = $TJOBS_MED_LAUDOS_MIRROR;

$TPRODUTOS_FOTOS = 'produtos_fotos';
$CONTPRODUTOS_FOTOS = $conn02;
$tabela_sis[133] = $TPRODUTOS_FOTOS;

$TNFMOD65SERIE000 = "nfmod65serie000_" . $info_cnpj_segmento;
$CONTNFMOD65SERIE000 = $conn02;
$tabela_sis[132] = $TNFMOD65SERIE000;

$TJOBS_APPCONVIDADOS = 'jobs_appconvidados';
$CONTJOBS_APPCONVIDADOS = $conn02;
$tabela_sis[131] = $TJOBS_APPCONVIDADOS;

$TUF_ICMS_INTERESTADUAL = 'uf_icms_interestadual';
$CONTUF_ICMS_INTERESTADUAL = $conn02;
$tabela_sis[130] = $TUF_ICMS_INTERESTADUAL;

$TTABELACEST = 'tabelacest';
$CONTTABELACEST = $conn02;
$tabela_sis[129] = $TTABELACEST;

$TCEE_HISTORICO = 'cee_historico';
$CONTCEE_HISTORICO = $conn02;
$tabela_sis[128] = $TCEE_HISTORICO;

$TCEE = 'cee';
$CONTCEE = $conn02;
$tabela_sis[127] = $TCEE;

$TJOBS_MED_LAUDOS_IMAGENS = 'jobs_med_laudos_imagens';
$CONTJOBS_MED_LAUDOS_IMAGENS = $conn02;
$tabela_sis[126] = $TJOBS_MED_LAUDOS_IMAGENS;

$TJOBS_MED_LAUDOS = 'jobs_med_laudos';
$CONTJOBS_MED_LAUDOS = $conn02;
$tabela_sis[125] = $TJOBS_MED_LAUDOS;

$TJOBS_MED_FUNCAO_PROFISSAO = 'jobs_med_funcao_profissao';
$CONTJOBS_MED_FUNCAO_PROFISSAO = $conn02;
$tabela_sis[124] = $TJOBS_MED_FUNCAO_PROFISSAO;

$TJOBS_MED_PROFISSAO = 'jobs_med_profissao';
$CONTJOBS_MED_PROFISSAO = $conn02;
$tabela_sis[123] = $TJOBS_MED_PROFISSAO;

$TJOBS_MED_COMBO_EXAMES = 'jobs_med_combo_exames';
$CONTJOBS_MED_COMBO_EXAMES = $conn02;
$tabela_sis[122] = $TJOBS_MED_COMBO_EXAMES;

$TJOBS_MED_BIOMETRIA = 'jobs_med_biometria';
$CONTJOBS_MED_BIOMETRIA = $conn02;
$tabela_sis[121] = $TJOBS_MED_BIOMETRIA;

$TJOBS_MED_TUSS = 'jobs_med_tuss';
$CONTJOBS_MED_TUSS = $conn02;
$tabela_sis[120] = $TJOBS_MED_TUSS;

$TJOBS_MED_OBS_EVENTOS = 'jobs_med_obs_eventos';
$CONTJOBS_MED_OBS_EVENTOS = $conn02;
$tabela_sis[119] = $TJOBS_MED_OBS_EVENTOS;

$TJOBS_MED_CID = 'jobs_med_cid';
$CONTJOBS_MED_CID = $conn02;
$tabela_sis[118] = $TJOBS_MED_CID;

$TJOBS_MED_USERS = 'jobs_med_users';
$CONTJOBS_MED_USERS = $conn02;
$tabela_sis[117] = $TJOBS_MED_USERS;

$TJOBS_MED_SKIN = 'jobs_med_skin';
$CONTJOBS_MED_SKIN = $conn02;
$tabela_sis[116] = $TJOBS_MED_SKIN;

$TCNPJCPF_FOTOS = 'cnpjcpf_fotos';
$CONTCNPJCPF_FOTOS = $conn02;
$tabela_sis[115] = $TCNPJCPF_FOTOS;

$TJOBS_EVENTOS_TIPO_JOBS = 'jobs_eventos_tipo_jobs';
$CONTJOBS_EVENTOS_TIPO_JOBS = $conn02;
$tabela_sis[114] = $TJOBS_EVENTOS_TIPO_JOBS;

$TJOBS_EVENTOS = "jobs_eventos";
$CONTJOBS_EVENTOS = $conn02;
$tabela_sis[113] = $TJOBS_EVENTOS;

$TPACOTESMED_INSUMOS = "pacotesmed_insumos";
$CONTPACOTESMED_INSUMOS = $conn02;
$tabela_sis[112] = $TPACOTESMED_INSUMOS;

$TPACOTESMED_FORMULA = "pacotesmed_formula";
$CONTPACOTESMED_FORMULA = $conn02;
$tabela_sis[111] = $TPACOTESMED_FORMULA;

$TNCM_MVA_CONVENIO_ST = "ncm_mva_convenio_st";
$CONTNCM_MVA_CONVENIO_ST = $conn02;
$tabela_sis[110] = $TNCM_MVA_CONVENIO_ST;

$TPRODUTOS_CELULAR_SERIAL = "produtos_celular_serial";
$CONTPRODUTOS_CELULAR_SERIAL = $conn02;
$tabela_sis[109] = $TPRODUTOS_CELULAR_SERIAL;

$TLMC_VENDAS = "lmc_vendas";
$CONTLMC_VENDAS = $conn02;
$tabela_sis[108] = $TLMC_VENDAS;

$TLMC_ITEM_FLUXO = "lmc_item_fluxo";
$CONTLMC_ITEM_FLUXO = $conn02;
$tabela_sis[107] = $TLMC_ITEM_FLUXO;

$TLMC_TANQUES = "lmc_tanques";
$CONTLMC_TANQUES = $conn02;
$tabela_sis[106] = $TLMC_TANQUES;

$TAPARTAMENTOS_HOSPEDES = "apartamentos_hospedes";
$CONTAPARTAMENTOS_HOSPEDES = $conn02;
$tabela_sis[105] = $TAPARTAMENTOS_HOSPEDES;

$TLIVROS_REGISTROS_GUIAS = "livros_registros_guias";
$CONTLIVROS_REGISTROS_GUIAS = $conn02;
$tabela_sis[104] = $TLIVROS_REGISTROS_GUIAS;

$TCNPJCPF_CONVENIADAS = "cnpjcpf_conveniadas";
$CONTCNPJCPF_CONVENIADAS = $conn02;
$tabela_sis[103] = $TCNPJCPF_CONVENIADAS;

$TAPARTAMENTOS_CLASSIFICA = "apartamentos_classifica";
$CONTAPARTAMENTOS_CLASSIFICA = $conn02;
$tabela_sis[102] = $TAPARTAMENTOS_CLASSIFICA;

$TAPARTAMENTOS = "apartamentos";
$CONTAPARTAMENTOS = $conn02;
$tabela_sis[101] = $TAPARTAMENTOS;

$TLOCALIZA = "localiza";
$CONTLOCALIZA = $conn02;
$tabela_sis[100] = $TLOCALIZA;

$TFRETE_ENDERECO_ENTREGA = "frete_endereco_entrega";
$CONTFRETE_ENDERECO_ENTREGA = $conn02;
$tabela_sis[99] = $TFRETE_ENDERECO_ENTREGA;

$TSPED_EFD_ICMS = "sped_efd_icms";
$CONTSPED_EFD_ICMS = $conn02;
$tabela_sis[98] = $TSPED_EFD_ICMS;

$TNFSMOD_GOVERNA = "nfsmod_governa";
$CONTNFSMOD_GOVERNA = $conn02;
$tabela_sis[97] = $TNFSMOD_GOVERNA;

$TCNPJCPF_LICENCIADAS_SETUP = "cnpjcpf_licenciadas_setup";
$CONTCNPJCPF_LICENCIADAS_SETUP = $conn02;
$tabela_sis[96] = $TCNPJCPF_LICENCIADAS_SETUP;

$TNFDOCUMENTOS_EVENTOS = "nfdocumentos_eventos";
$CONTNFDOCUMENTOS_EVENTOS = $conn02;
$tabela_sis[95] = $TNFDOCUMENTOS_EVENTOS;

$TLOGSECF = "logsecf";
$CONTLOGSECF = $conn02;
$tabela_sis[94] = $TLOGSECF;

$TETIQUETAS = "etiquetas";
$CONTETIQUETAS = $conn02;
$tabela_sis[93] = $TETIQUETAS;

$TFRETE_MANIFESTO = "frete_manifesto";
$CONTFRETE_MANIFESTO = $conn02;
$tabela_sis[92] = $TFRETE_MANIFESTO;

$TPRODUTOS_REAJUSTADOS = "produtos_reajustados";
$CONTPRODUTOS_REAJUSTADOS = $conn02;
$tabela_sis[91] = $TPRODUTOS_REAJUSTADOS;

$TACESSSEGMENTOS_MIRROR = "acesssegmentos_mirror";
$CONTACESSSEGMENTOS_MIRROR = $conn02;
$tabela_sis[90] = $TACESSSEGMENTOS_MIRROR;

$TJOBSATENCAO = "jobsatencao";
$CONTJOBSATENCAO = $conn02;
$tabela_sis[89] = $TJOBSATENCAO;

$TREQ_COMPRAS_FORNECEDORES = "req_compras_fornecedores";
$CONT_REQCOMPRAS_FORNECEDORES = $conn02;
$tabela_sis[88] = $TREQ_COMPRAS_FORNECEDORES;

$TREQ_COMPRAS = "req_compras";
$CONT_REQCOMPRAS = $conn02;
$tabela_sis[87] = $TREQ_COMPRAS;

$TSEGMENTOS_MIRROR = "segmentos_mirror";
$CONTSEGMENTOS_MIRROR = $conn02;
$tabela_sis[86] = $TSEGMENTOS_MIRROR;

$TSPED_PISCOFINS = "sped_piscofins";
$CONTSPED_PISCOFINS = $conn02;
$tabela_sis[85] = $TSPED_PISCOFINS;

$TPRODUTOS_BALANCO = "produtos_balanco";
$CONTPRODUTOS_BALANCO = $conn02;
$tabela_sis[84] = $TPRODUTOS_BALANCO;

$TPET_AGENDA = "pet_agendamento";
$CONTPET_AGENDA = $conn02;
$tabela_sis[83] = $TPET_AGENDA;

$TPET = "pet";
$CONTPET = $conn02;
$tabela_sis[82] = $TPET;

$TAPONTAMENTO_HS = "apontamento_hs";
$CONTAPONTAMENTO_HS = $conn02;
$tabela_sis[81] = $TAPONTAMENTO_HS;

$TCOMISSAO = "comissao";
$CONTCOMISSAO = $conn02;
$tabela_sis[80] = $TCOMISSAO;

$TCRM_PESQUISA_NATUREZAS = "crm_pesquisa_naturezas";
$CONTCRM_PESQUISA_NATUREZAS = $conn02;
$tabela_sis[79] = $TCRM_PESQUISA_NATUREZAS;

$TCRM_PESQUISA_RESPOSTAS = "crm_pesquisa_respostas";
$CONTCRM_PESQUISA_RESPOSTAS = $conn02;
$tabela_sis[78] = $TCRM_PESQUISA_RESPOSTAS;

$TCRM_PESQUISA_PERGUNTAS = "crm_pesquisa_perguntas";
$CONTCRM_PESQUISA_PERGUNTAS = $conn02;
$tabela_sis[77] = $TCRM_PESQUISA_PERGUNTAS;

$TLMC = "lmcc";
$CONTLMC = $conn02;
$tabela_sis[76] = $TLMC;

$TPRODUTOS_CUSTOMEDIO = "produtos_customedio";
$CONTPRODUTOS_CUSTOMEDIO = $conn02;
$tabela_sis[75] = $TPRODUTOS_CUSTOMEDIO;

$TPRODUTOS_APLICACAO = "produtos_aplicacao";
$CONTPRODUTOS_APLICACAO = $conn02;
$tabela_sis[74] = $TPRODUTOS_APLICACAO;

$TJOBSLEITURA = "jobsleitura";
$CONTJOBSLEITURA = $conn02;
$tabela_sis[73] = $TJOBSLEITURA;

$TLOGSBK = "logsbk";
$CONTLOGSBK = $conn02;
$tabela_sis[72] = $TLOGSBK;

$TFORMULA_LOTES = "formula_lotes";
$CONTFORMULA_LOTES = $conn02;
$tabela_sis[71] = $TFORMULA_LOTES;

$TFORMULA_INSUMOS = "formula_insumos";
$CONTFORMULA_INSUMOS = $conn02;
$tabela_sis[70] = $TFORMULA_INSUMOS;

$TFORMULA = "formula";
$CONTFORMULA = $conn02;
$tabela_sis[69] = $TFORMULA;

$TSEGMENTOS_IMG = "segmentos_img";
$CONTSEGMENTOS_IMG = $conn02;
$tabela_sis[68] = $TSEGMENTOS_IMG;

$TNCM = "ncm";
$CONTNCM = $conn02;
$tabela_sis[67] = $TNCM;

$TMAPA_RESUMO = "mapa_resumo";
$CONTMAPA_RESUMO = $conn02;
$tabela_sis[66] = $TMAPA_RESUMO;

$TDRE = "dre";
$CONTDRE = $conn02;
$tabela_sis[65] = $TDRE;

$TPRODUTOSNFCOMPLEMENTAR = "produtosnfcomplementar";
$CONTPRODUTOSNFCOMPLEMENTAR = $conn02;
$tabela_sis[64] = $TPRODUTOSNFCOMPLEMENTAR;

$TCENTROCUSTO_LANCAMENTOS = "centrocusto_lancamentos";
$CONTCENTROCUSTO_LANCAMENTOS = $conn02;
$tabela_sis[63] = $TCENTROCUSTO_LANCAMENTOS;

$TNF_OS = "nf_os_" . $info_cnpj_segmento;
$CONTNF_OS = $conn02;
$tabela_sis[62] = $TNF_OS;

$TNFMOD3A = "nfmod3a_" . $info_cnpj_segmento;
$CONTNFMOD3A = $conn02;
$tabela_sis[61] = $TNFMOD3A;

$TPRODUTOSDESPESAS = "produtosdespesas";
$CONTPRODUTOSDESPESAS = $conn02;
$tabela_sis[60] = $TPRODUTOSDESPESAS;

$TCNPJCPF_TRANSPORTADORA = "cnpjcpf_transportadora";
$CONTCNPJCPF_TRANSPORTADORA = $conn02;
$tabela_sis[59] = $TCNPJCPF_TRANSPORTADORA;

$TVEICULOS_DESPESAS = "veiculos_despesas";
$CONTVEICULOS_DESPESAS = $conn02;
$tabela_sis[58] = $TVEICULOS_DESPESAS;

$TNFMODDAV = "nfmoddav_" . $info_cnpj_segmento;
$CONTNFMODDAV = $conn02;
$tabela_sis[57] = $TNFMODDAV;

$TCNPJCPF_INSCRICAO = "cnpjcpf_inscricao";
$CONTCNPJCPF_INSCRICAO = $conn02;
$tabela_sis[56] = $TCNPJCPF_INSCRICAO;

$TCNPJCPF_ADICIONAL = "cnpjcpf_adicional";
$CONTCNPJCPF_ADICIONAL = $conn02;
$tabela_sis[55] = $TCNPJCPF_ADICIONAL;

$TVEICULOS = "veiculos";
$CONTVEICULOS = $conn02;
$tabela_sis[54] = $TVEICULOS;

$TJOBSCOLAB = "jobscolab";
$CONTJOBSCOLAB = $conn02;
$tabela_sis[53] = $TJOBSCOLAB;

$TJOBSMENSAGENS = "jobsmensagens";
$CONTJOBSMENSAGENS = $conn02;
$tabela_sis[52] = $TJOBSMENSAGENS;

$TJOBSGRUPOS = "jobsgrupos";
$CONTJOBSGRUPOS = $conn02;
$tabela_sis[51] = $TJOBSGRUPOS;

$TJOBSCONVID = "jobsconvid";
$CONTJOBSCONVID = $conn02;
$tabela_sis[50] = $TJOBSCONVID;

$TJOBS = "jobs";
$CONTJOBS = $conn02;
$tabela_sis[49] = $TJOBS;

$TCONTRATOS = "contratos";
$CONTCONTRATOS = $conn02;
$tabela_sis[48] = $TCONTRATOS;

$TLANCAMENTOSDEL = "lancamentosdel";
$CONTLANCAMENTOSDEL = $conn02;
$tabela_sis[47] = $TLANCAMENTOSDEL;

$TREQCOMPRAS = "reqcompras";
$CONTREQCOMPRAS = $conn02;
$tabela_sis[46] = $TREQCOMPRAS;

$TSETORES = "setores";
$CONTSETORES = $conn02;
$tabela_sis[45] = $TSETORES;

$TGRUPOPRODUTOS = "grupoprodutos";
$CONTGRUPOPRODUTOS = $conn02;
$tabela_sis[44] = $TGRUPOPRODUTOS;

$TINFOCPL = "infocpl";
$CONTINFOCPL = $conn02;
$tabela_sis[43] = $TINFOCPL;

$TLOGSMENSAGEM = "logsmensagem";
$CONTLOGSMENSAGEM = $conn02;
$tabela_sis[42] = $TLOGSMENSAGEM;

$TPAISES = "paises";
$CONTPAISES = $conn02;
$tabela_sis[41] = $TPAISES;

$TUNIDADE_FATOR_CONVERSAO = "unidade_fator_conversao";
$CONTUNIDADE_FATOR_CONVERSAO = $conn02;
$tabela_sis[40] = $TUNIDADE_FATOR_CONVERSAO;

$TNFMOD55SERIE900 = "nfmod55serie900_" . $info_cnpj_segmento;
$CONTNFMOD55SERIE900 = $conn02;
$tabela_sis[39] = $TNFMOD55SERIE900;

$TNFMOD55SERIE000 = "nfmod55serie000_" . $info_cnpj_segmento;
$CONTNFMOD55SERIE000 = $conn02;
$tabela_sis[38] = $TNFMOD55SERIE000;

$TNFDOCUMENTOS_TMP = "nfdocumentos_tmp";
$CONTNFDOCUMENTOS_TMP = $conn02;
$tabela_sis[37] = $TNFDOCUMENTOS_TMP;

$TNFDOCUMENTOS = "nfdocumentos";
$CONTNFDOCUMENTOS = $conn02;
$tabela_sis[36] = $TNFDOCUMENTOS;

$TPLANOCT_REFERENCIAL = "planoct_referencial";
$CONTPLANOCT_REFERENCIAL = $conn02;
$tabela_sis[35] = $TPLANOCT_REFERENCIAL;

$TITEM_FLUXO_TMP = "item_fluxo_tmp";
$CONTITEM_FLUXO_TMP = $conn02;
$tabela_sis[34] = $TITEM_FLUXO_TMP;

$TLANCAMENTOS_TMP = "lancamentos_tmp";
$CONTLANCAMENTOS_TMP = $conn02;
$tabela_sis[33] = $TLANCAMENTOS_TMP;

$TPRODUTOS = "produtos";
$CONTPRODUTOS = $conn02;
$tabela_sis[32] = $TPRODUTOS;

$TITEM_FLUXO = "item_fluxo";
$CONTITEM_FLUXO = $conn02;
$tabela_sis[31] = $TITEM_FLUXO;

$TPIS_COFINS = "pis_cofins";
$CONTPIS_COFINS = $conn02;
$tabela_sis[30] = $TPIS_COFINS;

$TSERVICOS_LST = "servicos_lst";
$CONTSERVICOS_LST = $conn02;
$tabela_sis[29] = $TSERVICOS_LST;

$TSERVICOS = "servicos";
$CONTSERVICOS = $conn02;
$tabela_sis[28] = $TSERVICOS;

$TNATUREZAOPERACAO = "naturezaoperacao";
$CONTNATUREZAOPERACAO = $conn02;
$tabela_sis[27] = $TNATUREZAOPERACAO;

$TCFOP = 'cfop';
$CONTCFOP = $conn02;
$tabela_sis[26] = $TCFOP;

$TTAB_MUNICIPIOS = 'tab_municipios';
$CONTTAB_MUNICIPIOS = $conn02;
$tabela_sis[25] = $TTAB_MUNICIPIOS;


$TCONTABILISTA = 'contabilista';
$CONTCONTABILISTA = $conn02;
$tabela_sis[24] = $TCONTABILISTA;

$TLICENCIADAS = 'licenciadas';
$CONTLICENCIADAS = $conn02;
$tabela_sis[23] = $TLICENCIADAS;

$TLUAREGISTRO = 'luaregistro';
$CONTLUAREGISTRO = $conn02;
$tabela_sis[22] = $TLUAREGISTRO;

$TLUAIMEI = 'luaimei';
$CONTLUAIMEI = $conn02;
$tabela_sis[21] = $TLUAIMEI;

$TLUAMAPS = 'luamaps';
$CONTLUAMAPS = $conn02;
$tabela_sis[20] = $TLUAMAPS;

$TLOGS = 'logs';
$CONTLOGS = $conn02;
$tabela_sis[19] = $TLOGS;

$TCNPJCPFOBSCRM = 'cnpjcpfobscrm';
$CONTCNPJCPFOBSCRM = $conn02;
$tabela_sis[18] = $TCNPJCPFOBSCRM;

$TMDE_LOGIN = 'mde_login';
$CONTMDE_LOGIN = $conn02;
$tabela_sis[17] = $TMDE_LOGIN;

$TSENHAS = 'senhas';
$CONTSENHAS = $conn02;
$tabela_sis[16] = $TSENHAS;

$TDOCFINANCEIROCNPJCPF = "docfinanceirocnpjcpf";
$CONTDOCFINANCEIROCNPJCPF = $conn02;
$tabela_sis[15] = $TDOCFINANCEIROCNPJCPF;

$TDOCFINANCEIRO = "docfinanceiro";
$CONTDOCFINANCEIRO = $conn02;
$tabela_sis[14] = $TDOCFINANCEIRO;

$TLUALANCA = 'lualanca';
$CONTLUALANCA = $conn02;
$tabela_sis[13] = $TLUALANCA;

$TAUTORIZACAO_DESCONTO = "autorizacao_desconto";
$CONTAUTORIZACAO_DESCONTO = $conn02;
$tabela_sis[12] = $TAUTORIZACAO_DESCONTO;

$TACESSSEGMENTOS = "acesssegmentos";
$CONTACESSSEGMENTOS = $conn02;
$tabela_sis[11] = $TACESSSEGMENTOS;

$TUNIDADENEGOCIO = "unidadenegocio";
$CONTUNIDADENEGOCIO = $conn02;
$tabela_sis[10] = $TUNIDADENEGOCIO;

$TPARCEIROS = "parceiros";
$CONTPARCEIROS = $conn02;
$tabela_sis[9] = $TPARCEIROS;

$THISTORICOS = "historicos";
$CONTHISTORICOS = $conn02;
$tabela_sis[8] = $THISTORICOS;

$TSEGMENTOS = "segmentos";
$CONTSEGMENTOS = $conn02;
$tabela_sis[7] = $TSEGMENTOS;

$TLANCAMENTOS = "lancamentos";
$CONTLANCAMENTOS = $conn02;
$tabela_sis[6] = $TLANCAMENTOS;

$TCNPJCPF = "cnpjcpf";
$CONTCNPJCPF = $conn02;
$tabela_sis[5] = $TCNPJCPF;

$TPLANOCT = "planoct";
$CONTPLANOCT = $conn02;
$tabela_sis[4] = $TPLANOCT;

$TCENTROCUSTO = "centrocusto";
$CONTCENTROCUSTO = $conn02;
$tabela_sis[3] = $TCENTROCUSTO;

$TPARAMETROS = "parametros";
$CONTPARAMETROS = $conn02;
$tabela_sis[2] = $TPARAMETROS;

$TVISITAS = "visitas";
$CONTVISITAS = $conn02;
$tabela_sis[1] = $TVISITAS;
$NIVEL_MOD = 2;