<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Illuminate\Support\Collection;

class ImportFailedException extends Exception
{
    protected Collection $failures;

    public function __construct(string $message = 'Import Data Gagal.', int $code = Response::HTTP_UNPROCESSABLE_ENTITY, Throwable $previous = null, Collection $failures = null)
    {
        parent::__construct($message, $code, $previous);
        $this->failures = $failures ?? collect();
    }

    public function getFailures(): Collection
    {
        return $this->failures;
    }

    public function render($request): RedirectResponse
    {
        return redirect()->back()
            ->withInput()
            ->withErrors(['import_error' => $this->getMessage()])
            ->with('import_failures', $this->getFailures());
    }
}
