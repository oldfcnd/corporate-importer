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

test('consegue importar as funções do arquivo corporativo', function () {
    // forçar a execução de duas queries em pontos distintos e testá-las
    config(['corporateimporter.maxupsert' => 2]);

    LotacaoImporter::make()
                    ->from($this->file_system->path($this->file_name))
                    ->execute();
    $lotacoes = Lotacao::get();

    expect($lotacoes)->toHaveCount(3)
    ->and($lotacoes->pluck('nome'))->toMatchArray(['Lotação 1', 'Lotação 2', 'Lotação 3'])
    ->and($lotacoes->pluck('sigla'))->toMatchArray(['Sigla 1', 'Sigla 2', 'Sigla 3']);
});

test('cria os logs para as lotações inválidas', function () {
    Log::shouldReceive('log')
        ->times(9)
        ->withArgs(function($level) {
            return $level === 'warning';
        }
    );

    LotacaoImporter::make()
                    ->from($this->file_system->path($this->file_name))
                    ->execute();

    expect(Lotacao::count())->toBe(3);
});