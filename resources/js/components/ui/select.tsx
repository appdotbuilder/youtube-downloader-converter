import React from 'react';

interface SelectProps {
  value: string;
  onValueChange: (value: string) => void;
  children: React.ReactNode;
}

interface SelectTriggerProps {
  children: React.ReactNode;
  className?: string;
}

interface SelectContentProps {
  children: React.ReactNode;
}

interface SelectItemProps {
  value: string;
  children: React.ReactNode;
}

interface SelectValueProps {
  placeholder?: string;
}

// Simple select implementation for this demo
export const Select = ({ value, onValueChange, children }: SelectProps) => {
  return (
    <div className="relative">
      <select
        className="flex h-10 w-full appearance-none rounded-md border border-gray-300 bg-white px-3 py-2 text-sm ring-offset-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
        value={value}
        onChange={(e) => onValueChange(e.target.value)}
      >
        <option value="" disabled>Choose format</option>
        {React.Children.map(children, (child) => {
          if (React.isValidElement<SelectItemProps>(child)) {
            return <option value={child.props.value}>{child.props.children}</option>;
          }
          return child;
        })}
      </select>
      <svg className="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
      </svg>
    </div>
  );
};

export const SelectTrigger = ({ children, className }: SelectTriggerProps) => {
  return <div className={className}>{children}</div>;
};

export const SelectContent = ({ children }: SelectContentProps) => {
  return <>{children}</>;
};

export const SelectItem = ({ value, children }: SelectItemProps) => {
  return <span data-value={value}>{children}</span>;
};

export const SelectValue = ({ placeholder }: SelectValueProps) => {
  return <span className="text-gray-500">{placeholder}</span>;
};