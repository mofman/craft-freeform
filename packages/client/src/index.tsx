import './config';

import React, { StrictMode } from 'react';
import ReactDOM from 'react-dom/client';
import { QueryClientProvider } from 'react-query';
import { ReactQueryDevtools } from 'react-query/devtools';
import { BrowserRouter, Route, Routes } from 'react-router-dom';

import App from './App';
import { CpNavigation } from './app/cp-navigation/cp-navigation';
import { Dashboard } from './app/pages/dashboard/dashboard';
import { Form, Forms, NewForm } from './app/pages/forms';
import { Settings } from './app/pages/settings/settings';
import { queryClient } from './config/query-client';
import { generateUrl } from './utils/urls';

const container = document.getElementById('freeform-client');
const root = ReactDOM.createRoot(container);

root.render(
  <StrictMode>
    <BrowserRouter basename={generateUrl('/client', false)}>
      <QueryClientProvider client={queryClient}>
        <ReactQueryDevtools />
        <CpNavigation />
        <Routes>
          <Route path="/" element={<App />}>
            <Route index element={<Dashboard />} />
            <Route path="forms">
              <Route path="new/*" element={<NewForm />} />
              <Route path=":id/*" element={<Form />} />
              <Route index element={<Forms />} />
            </Route>
            <Route path="settings" element={<Settings />} />
          </Route>
        </Routes>
      </QueryClientProvider>
    </BrowserRouter>
  </StrictMode>
);