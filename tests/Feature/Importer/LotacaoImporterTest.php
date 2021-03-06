<?php

/**
 * @see https://pestphp.com/docs/
 */

use Fcno\CorporateImporter\Importer\LotacaoImporter;
use Fcno\CorporateImporter\Models\Lotacao;
use Illuminate\Support\Facades\Log;

test('make retorna o objeto da classe', function () {
    expect(LotacaoImporter::make())->toBeInstanceOf(LotacaoImporter::class);
});

test('consegue importar as lotações do arquivo corporativo e cria os autorelacionamentos', function () {
    // forçar a execução de duas queries em pontos distintos e testá-las
    config(['corporateimporter.maxupsert' => 2]);

    LotacaoImporter::make()
        ->from($this->file_path)
        ->run();
    $lotacoes = Lotacao::get();

    expect($lotacoes)->toHaveCount(5)
    ->and($lotacoes->pluck('nome'))->toMatchArray(['Lotação 1', 'Lotação 2', 'Lotação 3', 'Lotação 4', 'Lotação 5'])
    ->and($lotacoes->pluck('sigla'))->toMatchArray(['Sigla 1', 'Sigla 2', 'Sigla 3', 'Sigla 4', 'Sigla 5'])
    ->and(Lotacao::has('lotacaoPai')->count())->toBe(2)
    ->and(Lotacao::has('lotacoesFilha')->count())->toBe(1)
    ->and(
        Lotacao::with('lotacoesFilha')
            ->find('1')
            ->lotacoesFilha
            ->pluck('nome')
    )->toMatchArray(['Lotação 3', 'Lotação 5'])
    ->and(
        Lotacao::with('lotacaoPai')
            ->find('1')
            ->nome
    )->toBe('Lotação 1');
});

test('cria os logs para as lotações inválidas', function () {
    Log::shouldReceive('log')
        ->times(18)
        ->withArgs(
            function ($level) {
                return $level === 'warning';
            }
        );

    LotacaoImporter::make()
                    ->from($this->file_path)
                    ->run();

    expect(Lotacao::count())->toBe(5);
});
